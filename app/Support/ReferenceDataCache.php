<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\CampaignDocumentType;
use App\Models\CmsPage;
use App\Models\Sector;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class ReferenceDataCache
{
    private const TTL_SECONDS = 3600;

    /**
     * @return Collection<int, CampaignCategory>
     */
    public static function campaignCategories(): Collection
    {
        return Cache::remember(
            'reference_data.campaign_categories',
            self::TTL_SECONDS,
            fn () => CampaignCategory::query()->orderBy('sort_order')->get(),
        );
    }

    /**
     * @return Collection<int, CampaignDocumentType>
     */
    public static function activeDocumentTypes(): Collection
    {
        return Cache::remember(
            'reference_data.document_types_active_ordered',
            self::TTL_SECONDS,
            fn () => CampaignDocumentType::activeOrdered()->get(),
        );
    }

    public static function cmsPage(string $slug): ?CmsPage
    {
        return Cache::remember(
            'reference_data.cms_page.'.$slug,
            self::TTL_SECONDS,
            fn () => CmsPage::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first(),
        );
    }

    /**
     * @return SupportCollection<string, Collection<int, Sector>>
     */
    public static function sectorsGroupedByCategory(): SupportCollection
    {
        return Cache::remember(
            'reference_data.sectors_grouped',
            self::TTL_SECONDS,
            fn () => Sector::query()
                ->orderBy('sort_order')
                ->get()
                ->groupBy('category'),
        );
    }

    /**
     * @return Collection<int, Sector>
     */
    public static function sectorsOrdered(): Collection
    {
        return Cache::remember(
            'reference_data.sectors_ordered',
            self::TTL_SECONDS,
            fn () => Sector::query()->orderBy('sort_order')->get(),
        );
    }

    public static function pendingCampaignsCount(): int
    {
        return Cache::remember(
            'reference_data.pending_campaigns_count',
            60,
            fn () => (int) Campaign::query()
                ->where('status', Campaign::STATUS_PENDING)
                ->count(),
        );
    }

    public static function siteSettings(): SiteSetting
    {
        return Cache::remember(
            'reference_data.site_settings',
            self::TTL_SECONDS,
            function (): SiteSetting {
                $settings = SiteSetting::query()->first();

                if ($settings) {
                    return $settings;
                }

                return SiteSetting::query()->create([
                    'notification_email' => 'admin@goodneighbors.ph',
                ]);
            },
        );
    }

    public static function forgetCampaignCategories(): void
    {
        Cache::forget('reference_data.campaign_categories');
    }

    public static function forgetDocumentTypes(): void
    {
        Cache::forget('reference_data.document_types_active_ordered');
    }

    public static function forgetCmsPage(?string $slug = null): void
    {
        if ($slug !== null) {
            Cache::forget('reference_data.cms_page.'.$slug);

            return;
        }

        CmsPage::query()->pluck('slug')->each(
            fn (string $pageSlug) => Cache::forget('reference_data.cms_page.'.$pageSlug),
        );
    }

    public static function forgetSectors(): void
    {
        Cache::forget('reference_data.sectors_grouped');
        Cache::forget('reference_data.sectors_ordered');
    }

    public static function forgetPendingCampaignsCount(): void
    {
        Cache::forget('reference_data.pending_campaigns_count');
    }

    public static function forgetSiteSettings(): void
    {
        Cache::forget('reference_data.site_settings');
    }
}
