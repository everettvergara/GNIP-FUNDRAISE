<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignImpactReportController;
use App\Http\Controllers\CampaignUser\ChangePasswordController;
use App\Http\Controllers\CampaignUser\DashboardController;
use App\Http\Controllers\CampaignUser\MyCampaignController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FundraiserProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');

Route::get('/fundraisers/{user}', [FundraiserProfileController::class, 'show'])->name('fundraisers.show');

Route::get('/announcements', [CmsPageController::class, 'announcements'])->name('announcements.index');
Route::get('/our-sectors', [CmsPageController::class, 'sectors'])->name('sectors.index');
Route::get('/our-sectors/{slug}', [CmsPageController::class, 'sector'])->name('sectors.show');
Route::get('/partners', [CmsPageController::class, 'partners'])->name('partners.index');

Route::get('/faq', fn () => app(CmsPageController::class)->show('faq'))->name('faq');
Route::get('/support', fn () => app(CmsPageController::class)->show('support'))->name('support');
Route::get('/fundraising-tips', fn () => app(CmsPageController::class)->show('fundraising-tips'))->name('fundraising-tips');
Route::get('/terms-of-use', fn () => app(CmsPageController::class)->show('terms-of-use'))->name('terms-of-use');
Route::get('/terms-and-conditions', fn () => app(CmsPageController::class)->show('terms-and-conditions'))->name('terms-and-conditions');
Route::get('/privacy-policy', fn () => app(CmsPageController::class)->show('privacy-policy'))->name('privacy-policy');
Route::get('/donor-policy', fn () => app(CmsPageController::class)->show('donor-policy'))->name('donor-policy');

Route::post('/webhooks/xendit', function () {
    return response()->json(['status' => 'received']);
})->name('webhooks.xendit');

/*
|--------------------------------------------------------------------------
| Campaign user portal (auth + verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('portal.module:dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('portal.module:profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/account/change-password', [ChangePasswordController::class, 'edit'])->name('account.change-password');
        Route::put('/account/change-password', [ChangePasswordController::class, 'update'])->name('account.change-password.update');

        Route::get('/account/settings', function () {
            return view('campaign-user.settings');
        })->name('account.settings');
    });

    Route::middleware('portal.module:my_campaigns')->group(function () {
        Route::get('/my-campaigns', [MyCampaignController::class, 'index'])->name('my-campaigns.index');
    });

    Route::middleware('portal.module:campaigns_create')->group(function () {
        Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    });

    Route::middleware('portal.module:campaigns_manage')->group(function () {
        Route::get('/campaigns/{slug}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{slug}', [CampaignController::class, 'update'])->name('campaigns.update');
        Route::post('/campaigns/{slug}/submit-for-approval', [CampaignController::class, 'submitForApproval'])->name('campaigns.submit-for-approval');
        Route::post('/campaigns/{slug}/withdraw-submission', [CampaignController::class, 'withdrawSubmission'])->name('campaigns.withdraw-submission');
        Route::post('/campaigns/{slug}/documents/{documentType}', [CampaignController::class, 'uploadDocument'])->name('campaigns.documents.store');
        Route::delete('/campaigns/{slug}/documents/{documentType}', [CampaignController::class, 'destroyDocument'])->name('campaigns.documents.destroy');
        Route::delete('/campaigns/{slug}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
        Route::get('/campaigns/{slug}/share', [CampaignController::class, 'share'])->name('campaigns.share');
    });

    Route::middleware('portal.module:impact_reports')->group(function () {
        Route::post('/campaigns/{slug}/impact-reports', [CampaignImpactReportController::class, 'store'])->name('campaigns.impact-reports.store');
        Route::delete('/campaigns/{slug}/impact-reports/{impactReport}', [CampaignImpactReportController::class, 'destroy'])->name('campaigns.impact-reports.destroy');
    });

    Route::middleware('portal.module:donations')->group(function () {
        Route::get('/donations', [DashboardController::class, 'donations'])->name('donations.index');
        Route::get('/donations/{donation}/thank-you', function () {
            return view('campaign-user.thank-you');
        })->name('donations.thank-you');
    });
});

/*
|--------------------------------------------------------------------------
| Public campaign routes (after auth-specific campaign routes)
|--------------------------------------------------------------------------
*/

Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');
Route::get('/campaigns/{slug}/donate', [DonationController::class, 'create'])->name('donations.create');
Route::post('/campaigns/{slug}/donate', [DonationController::class, 'store'])->name('donations.store');

require __DIR__.'/auth.php';
