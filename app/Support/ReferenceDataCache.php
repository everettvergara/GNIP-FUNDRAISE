<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\CampaignDocumentType;
use App\Models\CmsPage;
use App\Models\Sector;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class ReferenceDataCache
{
    private const TTL_SECONDS = 3600;

    private const KEY_PREFIX = 'reference_data.v2.';

    /**
     * @return Collection<int, CampaignCategory>
     */
    public static function campaignCategories(): Collection
    {
        $rows = Cache::remember(
            self::KEY_PREFIX.'campaign_categories',
            self::TTL_SECONDS,
            fn () => CampaignCategory::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (CampaignCategory $category) => $category->getAttributes())
                ->all(),
        );

        return self::hydrateMany(CampaignCategory::class, $rows);
    }

    /**
     * @return Collection<int, CampaignDocumentType>
     */
    public static function activeDocumentTypes(): Collection
    {
        $rows = Cache::remember(
            self::KEY_PREFIX.'document_types_active_ordered',
            self::TTL_SECONDS,
            fn () => CampaignDocumentType::activeOrdered()
                ->get()
                ->map(fn (CampaignDocumentType $type) => $type->getAttributes())
                ->all(),
        );

        return self::hydrateMany(CampaignDocumentType::class, $rows);
    }

    public static function cmsPage(string $slug): ?CmsPage
    {
        $attributes = Cache::remember(
            self::KEY_PREFIX.'cms_page.'.$slug,
            self::TTL_SECONDS,
            function () use ($slug): ?array {
                $page = CmsPage::query()
                    ->where('slug', $slug)
                    ->where('is_published', true)
                    ->first();

                return $page?->getAttributes();
            },
        );

        return self::hydrateOne(CmsPage::class, $attributes);
    }

    /**
     * @return SupportCollection<string, Collection<int, Sector>>
     */
    public static function sectorsGroupedByCategory(): SupportCollection
    {
        /** @var array<string, array<int, array<string, mixed>>> $grouped */
        $grouped = Cache::remember(
            self::KEY_PREFIX.'sectors_grouped',
            self::TTL_SECONDS,
            function (): array {
                return Sector::query()
                    ->orderBy('sort_order')
                    ->get()
                    ->groupBy('category')
                    ->map(fn (Collection $sectors) => $sectors
                        ->map(fn (Sector $sector) => $sector->getAttributes())
                        ->all())
                    ->all();
            },
        );

        return collect($grouped)->map(
            fn (array $rows) => self::hydrateMany(Sector::class, $rows),
        );
    }

    /**
     * @return Collection<int, Sector>
     */
    public static function sectorsOrdered(): Collection
    {
        $rows = Cache::remember(
            self::KEY_PREFIX.'sectors_ordered',
            self::TTL_SECONDS,
            fn () => Sector::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Sector $sector) => $sector->getAttributes())
                ->all(),
        );

        return self::hydrateMany(Sector::class, $rows);
    }

    public static function pendingCampaignsCount(): int
    {
        return Cache::remember(
            self::KEY_PREFIX.'pending_campaigns_count',
            60,
            fn () => (int) Campaign::query()
                ->where('status', Campaign::STATUS_PENDING)
                ->count(),
        );
    }

    public static function siteSettings(): SiteSetting
    {
        $attributes = Cache::remember(
            self::KEY_PREFIX.'site_settings',
            self::TTL_SECONDS,
            function (): array {
                $settings = SiteSetting::query()->first();

                if ($settings) {
                    return $settings->getAttributes();
                }

                return SiteSetting::query()->create([
                    'notification_email' => 'admin@goodneighbors.ph',
                ])->getAttributes();
            },
        );

        return self::hydrateOne(SiteSetting::class, $attributes);
    }

    public static function forgetCampaignCategories(): void
    {
        Cache::forget(self::KEY_PREFIX.'campaign_categories');
    }

    public static function forgetDocumentTypes(): void
    {
        Cache::forget(self::KEY_PREFIX.'document_types_active_ordered');
    }

    public static function forgetCmsPage(?string $slug = null): void
    {
        if ($slug !== null) {
            Cache::forget(self::KEY_PREFIX.'cms_page.'.$slug);

            return;
        }

        CmsPage::query()->pluck('slug')->each(
            fn (string $pageSlug) => Cache::forget(self::KEY_PREFIX.'cms_page.'.$pageSlug),
        );
    }

    public static function forgetSectors(): void
    {
        Cache::forget(self::KEY_PREFIX.'sectors_grouped');
        Cache::forget(self::KEY_PREFIX.'sectors_ordered');
    }

    public static function forgetPendingCampaignsCount(): void
    {
        Cache::forget(self::KEY_PREFIX.'pending_campaigns_count');
    }

    public static function forgetSiteSettings(): void
    {
        Cache::forget(self::KEY_PREFIX.'site_settings');
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private static function hydrateOne(string $modelClass, ?array $attributes): ?Model
    {
        if ($attributes === null) {
            return null;
        }

        return (new $modelClass)->newFromBuilder($attributes);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<int, array<string, mixed>>  $rows
     * @return Collection<int, Model>
     */
    private static function hydrateMany(string $modelClass, array $rows): Collection
    {
        return $modelClass::hydrate($rows);
    }
}
