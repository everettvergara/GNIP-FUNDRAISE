<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignDocumentRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\CampaignDocumentType;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $categories = CampaignCategory::query()->orderBy('sort_order')->get();

        $selectedCategory = $request->string('category')->toString();

        if ($selectedCategory !== '' && ! $categories->contains('slug', $selectedCategory)) {
            $selectedCategory = '';
        }

        $campaigns = Campaign::query()
            ->with(['user', 'category'])
            ->where('status', Campaign::STATUS_ACTIVE)
            ->when($selectedCategory !== '', function ($query) use ($selectedCategory) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $selectedCategory));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(string $slug): View
    {
        $campaign = Campaign::query()
            ->with(['user', 'category', 'media', 'impactReports.photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($campaign->status !== Campaign::STATUS_ACTIVE && (! auth()->check() || auth()->id() !== $campaign->user_id)) {
            abort(404);
        }

        $isOwner = auth()->check() && auth()->id() === $campaign->user_id;

        $donationsQuery = $campaign->donations()->latest();

        if (! $isOwner) {
            $donationsQuery
                ->where('status', Donation::STATUS_CONFIRMED)
                ->where('show_name', true);
        }

        $donations = $donationsQuery->paginate(10);

        return view('campaigns.show', compact('campaign', 'isOwner', 'donations'));
    }

    public function create(): View
    {
        return view('campaigns.create', [
            'categories' => CampaignCategory::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $slug = $this->uniqueSlug($request->validated('title'));

        $campaign = Campaign::query()->create([
            ...$request->safe()->except(['cover_image', 'gallery_images']),
            'user_id' => $request->user()->id,
            'slug' => $slug,
            'status' => Campaign::STATUS_DRAFT,
            'raised_amount' => 0,
        ]);

        if ($request->hasFile('cover_image')) {
            $campaign->update([
                'cover_image' => $request->file('cover_image')->store('campaigns', 'public'),
            ]);
        }

        $this->syncGallery($campaign, $request);

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-created');
    }

    public function edit(string $slug): View
    {
        $campaign = Campaign::query()
            ->with(['media', 'documents.documentType'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('view', $campaign);

        $documentTypes = CampaignDocumentType::activeOrdered()->get();
        $missingRequiredDocuments = $campaign->missingRequiredDocumentTypes();

        return view('campaigns.edit', [
            'campaign' => $campaign,
            'categories' => CampaignCategory::query()->orderBy('sort_order')->get(),
            'documentTypes' => $documentTypes,
            'missingRequiredDocuments' => $missingRequiredDocuments,
            'canEdit' => $campaign->canBeEditedByOwner(),
        ]);
    }

    public function update(UpdateCampaignRequest $request, string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('update', $campaign);

        $data = $request->safe()->except(['cover_image', 'gallery_images', 'remove_gallery']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('campaigns', 'public');
        }

        if ($request->validated('title') !== $campaign->title) {
            $data['slug'] = $this->uniqueSlug($request->validated('title'), $campaign->id);
        }

        $campaign->update($data);

        $this->syncGallery($campaign, $request);

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-updated');
    }

    public function uploadDocument(StoreCampaignDocumentRequest $request, string $slug, CampaignDocumentType $documentType): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('uploadDocument', $campaign);

        if (! $documentType->is_active) {
            abort(404);
        }

        $file = $request->file('document');
        $path = $file->store('campaigns/documents', 'public');

        $existing = $campaign->documents()
            ->where('document_type_id', $documentType->id)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->path);
            $existing->update([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        } else {
            $campaign->documents()->create([
                'document_type_id' => $documentType->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-document-uploaded');
    }

    public function destroyDocument(string $slug, CampaignDocumentType $documentType): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('uploadDocument', $campaign);

        $document = $campaign->documents()
            ->where('document_type_id', $documentType->id)
            ->first();

        if ($document) {
            $document->delete();
        }

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-document-removed');
    }

    public function submitForApproval(string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('submitForApproval', $campaign);

        $campaign->update([
            'status' => Campaign::STATUS_PENDING,
            'submitted_at' => now(),
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-submitted-for-approval');
    }

    public function share(string $slug): View
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('view', $campaign);

        return view('campaigns.share', compact('campaign'));
    }

    public function destroy(string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('delete', $campaign);

        $campaign->delete();

        return redirect()
            ->route('my-campaigns.index')
            ->with('status', 'campaign-deleted');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Campaign::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function syncGallery(Campaign $campaign, Request $request): void
    {
        if ($request->has('remove_gallery')) {
            $campaign->media()
                ->whereIn('id', $request->input('remove_gallery', []))
                ->get()
                ->each(function ($media) {
                    Storage::disk('public')->delete($media->path);
                    $media->delete();
                });
        }

        if ($request->hasFile('gallery_images')) {
            $sortOrder = (int) $campaign->media()->max('sort_order');

            foreach ($request->file('gallery_images') as $file) {
                $sortOrder++;
                $campaign->media()->create([
                    'path' => $file->store('campaigns/gallery', 'public'),
                    'type' => 'image',
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }
}
