<?php

use Workdo\BeautySpaManagement\Http\Controllers\BeautyBookingController;
use Workdo\BeautySpaManagement\Http\Controllers\BookingOrderController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyLoyaltyProgramController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyServiceOfferController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyMembershipController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyCustomPageController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyCertificationController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyTrainingController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyGiftCardController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyServiceTypeController;
use Workdo\BeautySpaManagement\Http\Controllers\BrandSettingController;
use Workdo\BeautySpaManagement\Http\Controllers\BannerSectionController;
use Workdo\BeautySpaManagement\Http\Controllers\FeatureSectionController;
use Workdo\BeautySpaManagement\Http\Controllers\TestimonialController;
use Workdo\BeautySpaManagement\Http\Controllers\AboutSectionController;
use Workdo\BeautySpaManagement\Http\Controllers\HomeSectionController;
use Workdo\BeautySpaManagement\Http\Controllers\ContactInfoController;
use Workdo\BeautySpaManagement\Http\Controllers\SocialLinksController;
use Illuminate\Support\Facades\Route;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyServiceController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyWorkingController;
use Workdo\BeautySpaManagement\Http\Controllers\DashboardController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyFrontendController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautySubscriberController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyContactController;
use Workdo\BeautySpaManagement\Http\Controllers\BeautyReviewController;
use Workdo\BeautySpaManagement\Http\Middleware\BeautySpaSharedDataMiddleware;

// Frontend Routes (Public) - Multi-tenant support
Route::middleware(['web', BeautySpaSharedDataMiddleware::class])->prefix('{userSlug?}/beauty-spa')->name('beauty-spa.')->group(function () {
    Route::get('/', [BeautyFrontendController::class, 'index'])->name('home');
    Route::get('/services', [BeautyFrontendController::class, 'services'])->name('services');
    Route::get('/service/{service}', [BeautyFrontendController::class, 'serviceDetail'])->name('service.detail');
    Route::post('/service/{service}/review', [BeautyFrontendController::class, 'storeReview'])->name('service.review.store');
    Route::get('/booking', [BeautyFrontendController::class, 'booking'])->name('booking');
    Route::post('/booking', [BeautyFrontendController::class, 'bookingStore'])->name('booking.store');
    Route::post('/check-holiday', [BeautyFrontendController::class, 'checkHoliday'])->name('check-holiday');
    Route::post('/validate-slot-capacity', [BeautyFrontendController::class, 'validateSlotCapacity'])->name('validate-slot-capacity');
    Route::get('/contact', [BeautyFrontendController::class, 'contact'])->name('contact');
    Route::post('/contact', [BeautyFrontendController::class, 'contactStore'])->name('contact.store');
    Route::get('/about', [BeautyFrontendController::class, 'about'])->name('about');
    Route::get('/booking-success', [BeautyFrontendController::class, 'bookingSuccess'])->name('booking-success');
    Route::get('/page/{slug}', [BeautyFrontendController::class, 'customPage'])->name('custom-page');
    Route::post('/subscribe', [BeautyFrontendController::class, 'subscribe'])->name('subscribe');
});

// Backend Routes (Authenticated)
Route::middleware(['web', 'auth', 'verified', 'PlanModuleCheck:BeautySpaManagement'])->group(function () {
    Route::get('/beauty-spa-management', [DashboardController::class, 'index'])->name('beauty-spa-management.index');

    Route::prefix('beauty-spa-management/service-types')->name('beauty-spa-management.service-types.')->group(function () {
        Route::get('/', [BeautyServiceTypeController::class, 'index'])->name('index');
        Route::post('/', [BeautyServiceTypeController::class, 'store'])->name('store');
        Route::put('/{servicetype}', [BeautyServiceTypeController::class, 'update'])->name('update');
        Route::delete('/{servicetype}', [BeautyServiceTypeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/gift-cards')->name('beauty-spa-management.gift-cards.')->group(function () {
        Route::get('/', [BeautyGiftCardController::class, 'index'])->name('index');
        Route::post('/', [BeautyGiftCardController::class, 'store'])->name('store');
        Route::put('/{giftcard}', [BeautyGiftCardController::class, 'update'])->name('update');
        Route::delete('/{giftcard}', [BeautyGiftCardController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/working-hours')->name('beauty-spa-management.working-hours.')->group(function () {
        Route::get('/', [BeautyWorkingController::class, 'index'])->name('index');
        Route::post('/', [BeautyWorkingController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/trainings')->name('beauty-spa-management.trainings.')->group(function () {
        Route::get('/', [BeautyTrainingController::class, 'index'])->name('index');
        Route::post('/', [BeautyTrainingController::class, 'store'])->name('store');
        Route::put('/{training}', [BeautyTrainingController::class, 'update'])->name('update');
        Route::delete('/{training}', [BeautyTrainingController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/certifications')->name('beauty-spa-management.certifications.')->group(function () {
        Route::get('/', [BeautyCertificationController::class, 'index'])->name('index');
        Route::post('/', [BeautyCertificationController::class, 'store'])->name('store');
        Route::put('/{certification}', [BeautyCertificationController::class, 'update'])->name('update');
        Route::delete('/{certification}', [BeautyCertificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/custom-pages')->name('beauty-spa-management.custom-pages.')->group(function () {
        Route::get('/', [BeautyCustomPageController::class, 'index'])->name('index');
        Route::post('/', [BeautyCustomPageController::class, 'store'])->name('store');
        Route::put('/{custompage}', [BeautyCustomPageController::class, 'update'])->name('update');
        Route::delete('/{custompage}', [BeautyCustomPageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/services')->name('beauty-spa-management.services.')->group(function () {
        Route::get('/', [BeautyServiceController::class, 'index'])->name('index');
        Route::post('/', [BeautyServiceController::class, 'store'])->name('store');
        Route::put('/{service}', [BeautyServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [BeautyServiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/beauty-memberships')->name('beauty-spa-management.beauty-memberships.')->group(function () {
        Route::get('/', [BeautyMembershipController::class, 'index'])->name('index');
        Route::post('/', [BeautyMembershipController::class, 'store'])->name('store');
        Route::put('/{beautymembership}', [BeautyMembershipController::class, 'update'])->name('update');
        Route::delete('/{beautymembership}', [BeautyMembershipController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/beauty-service-offers')->name('beauty-spa-management.beauty-service-offers.')->group(function () {
        Route::get('/', [BeautyServiceOfferController::class, 'index'])->name('index');
        Route::post('/', [BeautyServiceOfferController::class, 'store'])->name('store');
        Route::put('/{beautyserviceoffer}', [BeautyServiceOfferController::class, 'update'])->name('update');
        Route::delete('/{beautyserviceoffer}', [BeautyServiceOfferController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/beauty-loyalty-programs')->name('beauty-spa-management.beauty-loyalty-programs.')->group(function () {
        Route::get('/', [BeautyLoyaltyProgramController::class, 'index'])->name('index');
        Route::post('/', [BeautyLoyaltyProgramController::class, 'store'])->name('store');
        Route::put('/{beautyloyaltyprogram}', [BeautyLoyaltyProgramController::class, 'update'])->name('update');
        Route::delete('/{beautyloyaltyprogram}', [BeautyLoyaltyProgramController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/brand-settings')->name('beauty-spa-management.brand-settings.')->group(function () {
        Route::get('/', [BrandSettingController::class, 'index'])->name('index');
        Route::post('/', [BrandSettingController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/banner-section')->name('beauty-spa-management.banner-section.')->group(function () {
        Route::get('/', [BannerSectionController::class, 'index'])->name('index');
        Route::post('/', [BannerSectionController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/home-section')->name('beauty-spa-management.home-section.')->group(function () {
        Route::get('/', [HomeSectionController::class, 'index'])->name('index');
        Route::post('/', [HomeSectionController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/about-section')->name('beauty-spa-management.about-section.')->group(function () {
        Route::get('/', [AboutSectionController::class, 'index'])->name('index');
        Route::post('/', [AboutSectionController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/contact-info')->name('beauty-spa-management.contact-info.')->group(function () {
        Route::get('/', [ContactInfoController::class, 'index'])->name('index');
        Route::post('/', [ContactInfoController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/social-links')->name('beauty-spa-management.social-links.')->group(function () {
        Route::get('/', [SocialLinksController::class, 'index'])->name('index');
        Route::post('/', [SocialLinksController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/feature-section')->name('beauty-spa-management.feature-section.')->group(function () {
        Route::get('/', [FeatureSectionController::class, 'index'])->name('index');
        Route::post('/', [FeatureSectionController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/testimonials')->name('beauty-spa-management.testimonials.')->group(function () {
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::post('/', [TestimonialController::class, 'store'])->name('store');
    });

    Route::prefix('beauty-spa-management/beauty-bookings')->name('beauty-spa-management.beauty-bookings.')->group(function () {
        Route::get('/', [BeautyBookingController::class, 'index'])->name('index');
        Route::post('/', [BeautyBookingController::class, 'store'])->name('store');
        Route::put('/{beautybooking}', [BeautyBookingController::class, 'update'])->name('update');
        Route::delete('/{beautybooking}', [BeautyBookingController::class, 'destroy'])->name('destroy');
        Route::post('/payments', [BeautyBookingController::class, 'storePayment'])->name('payments.store');
        Route::post('/get-service-price', [BeautyFrontendController::class, 'getServicePrice'])->name('get-service-price');
        Route::post('/check-holiday', [BeautyFrontendController::class, 'checkHoliday'])->name('check-holiday');
        Route::post('/validate-slot-capacity', [BeautyFrontendController::class, 'validateSlotCapacity'])->name('validate-slot-capacity');
    });

    Route::prefix('beauty-spa-management/beauty-booking-payments')->name('beauty-spa-management.beauty-bookings.payments.')->group(function () {
        Route::get('/', [BeautyBookingController::class, 'paymentsIndex'])->name('index');
        Route::delete('/{payment}', [BeautyBookingController::class, 'destroyPayment'])->name('destroy');
        Route::post('/{payment}/mark-paid', [BeautyBookingController::class, 'markPaid'])->name('mark-paid');
    });

    Route::prefix('beauty-spa-management/booking-order')->name('beauty-spa-management.booking-order.')->group(function () {
        Route::get('/', [BookingOrderController::class, 'index'])->name('index');
        Route::post('/update-status', [BookingOrderController::class, 'updateStatus'])->name('update-status');
    });

    Route::prefix('beauty-spa-management/beauty-receipt')->name('beauty-spa-management.beauty-receipt.')->group(function () {
        Route::get('/', [BookingOrderController::class, 'receiptIndex'])->name('index');
        Route::get('/{receipt}/download', [BookingOrderController::class, 'receiptDownload'])->name('download');
    });

    Route::prefix('beauty-spa-management/beauty-subscribers')->name('beauty-spa-management.beauty-subscribers.')->group(function () {
        Route::get('/', [BeautySubscriberController::class, 'index'])->name('index');
        Route::delete('/{beautysubscriber}', [BeautySubscriberController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('beauty-spa-management/beauty-contacts')->name('beauty-spa-management.beauty-contacts.')->group(function () {
        Route::get('/', [BeautyContactController::class, 'index'])->name('index');
        Route::delete('/{beautycontact}', [BeautyContactController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('beauty-spa-management/beauty-reviews')->name('beauty-spa-management.beauty-reviews.')->group(function () {
        Route::get('/', [BeautyReviewController::class, 'index'])->name('index');
        Route::delete('/{beautyreview}', [BeautyReviewController::class, 'destroy'])->name('destroy');
    });
});
