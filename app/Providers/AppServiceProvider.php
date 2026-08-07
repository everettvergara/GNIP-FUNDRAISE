<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\CampaignImpactReport;
use App\Models\Donation;
use App\Observers\DonationObserver;
use App\Policies\CampaignImpactReportPolicy;
use App\Policies\CampaignPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(CampaignImpactReport::class, CampaignImpactReportPolicy::class);

        Paginator::defaultView('vendor.pagination.gn');
        Paginator::defaultSimpleView('vendor.pagination.gn');

        Donation::observe(DonationObserver::class);
    }
}
