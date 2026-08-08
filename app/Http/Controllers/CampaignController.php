<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignDocumentRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignDocumentType;
use App\Models\CampaignEvent;
use App\Models\Donation;
use App\Models\User;
use App\Services\CampaignNotificationService;
use App\Support\ReferenceDataCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignNotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $categories = ReferenceDataCache::campaignCategories();

        $selectedCategory = $request->string('category')->toString();

        if ($selectedCategory !== '' && ! $categories->contains('slug', $selectedCategory)) {
            $selectedCategory = '';
        }

        $campaigns = Campaign::query()
            ->with(['user', 'category'])
            ->publiclyListed()
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
            ->publiclyListed()
            ->firstOrFail();

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
        $this->authorize('create', Campaign::class);

        return view('campaigns.create', [
            'categories' => ReferenceDataCache::campaignCategories(),
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $slug = $this->uniqueSlug($request->validated('title'));

        $campaign = Campaign::query()->create([
            ...$request->safe()->except(['cover_image', 'gallery_images', 'status']),
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
            ->with(['media', 'documents.documentType', 'events.user', 'events.admin'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('view', $campaign);

        $documentTypes = ReferenceDataCache::activeDocumentTypes();
        $missingRequiredDocuments = $campaign->missingRequiredDocumentTypes($documentTypes);

        return view('campaigns.edit', [
            'campaign' => $campaign,
            'categories' => ReferenceDataCache::campaignCategories(),
            'documentTypes' => $documentTypes,
            'missingRequiredDocuments' => $missingRequiredDocuments,
            'canEdit' => $campaign->canBeEditedByOwner(),
            'isReadOnly' => $campaign->canWithdrawSubmission(),
            'canSubmitForApproval' => $campaign->canSubmitForApproval($documentTypes),
            'canBeDeleted' => ! $campaign->donations()->exists(),
        ]);
    }

    public function update(UpdateCampaignRequest $request, string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('update', $campaign);

        $data = $request->safe()->except(['cover_image', 'gallery_images', 'remove_gallery', 'status', 'submit_for_approval']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('campaigns', 'public');
        }

        if ($request->validated('title') !== $campaign->title) {
            $data['slug'] = $this->uniqueSlug($request->validated('title'), $campaign->id);
        }

        $campaign->update($data);

        $this->syncGallery($campaign, $request);

        if ($request->boolean('submit_for_approval')) {
            $campaign->refresh();

            if (! $campaign->canSubmitForApproval()) {
                return redirect()
                    ->route('campaigns.edit', $campaign->slug)
                    ->withErrors([
                        'submit_for_approval' => 'Upload all required documents before submitting for approval.',
                    ]);
            }

            $this->authorize('submitForApproval', $campaign);

            $this->markCampaignSubmittedForApproval($campaign, $request->user(), $request->validated('submission_comment'));

            return redirect()
                ->route('campaigns.edit', $campaign->slug)
                ->with('status', 'campaign-submitted-for-approval');
        }

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

        $this->markCampaignSubmittedForApproval($campaign, auth()->user());

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-submitted-for-approval');
    }

    public function withdrawSubmission(string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        $this->authorize('withdrawSubmission', $campaign);

        $campaign->update([
            'status' => Campaign::STATUS_DRAFT,
            'submitted_at' => null,
        ]);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_WITHDRAWN,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('campaigns.edit', $campaign->slug)
            ->with('status', 'campaign-submission-withdrawn');
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

    private function markCampaignSubmittedForApproval(Campaign $campaign, ?User $user = null, ?string $comment = null): void
    {
        $campaign->update([
            'status' => Campaign::STATUS_PENDING,
            'submitted_at' => now(),
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_SUBMITTED,
            'comment' => $comment,
            'user_id' => $user?->id ?? auth()->id(),
        ]);

        $this->notifications->sendSubmittedToAdmin($campaign);
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
