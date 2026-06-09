<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSetup;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;
use Workdo\PhotoStudioManagement\Http\Requests\StoreBrandSettingRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreBannerSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreAboutSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreTitleSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreTestimonialRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreGallerySectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreAwardSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreMediaSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreFaqSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreContactSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreFooterSectionRequest;
use Workdo\PhotoStudioManagement\Http\Requests\StoreDashboardWelcomeCardRequest;

class PhotoStudioSetupController extends Controller
{
    // Brand Settings
    public function brandIndex()
    {
        if (Auth::user()->can('manage-photo-studio-brand-settings')) {
            $settings = PhotoStudioSetup::where('created_by', creatorId())
                ->whereIn('key', ['logo', 'footer_logo', 'favicon', 'site_title', 'footer_text', 'footer_description', 'copy_link_card_title', 'copy_link_card_description', 'copy_link_button_text', 'copy_link_button_icon'])
                ->pluck('value', 'key')
                ->toArray();

            return Inertia::render('PhotoStudioManagement/SystemSetup/brand-settings', [
                'settings' => $settings,
            ]);
        } else {

            return back()->with('error', __('Permission denied.'));
        }
    }

    public function brandStore(StoreBrandSettingRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-brand-settings')) {
            $validated = $request->validated();

            $settings = [
                'logo'               => !empty($validated['logo']) ? basename($validated['logo']) : null,
                'footer_logo'        => !empty($validated['footer_logo']) ? basename($validated['footer_logo']) : null,
                'favicon'            => !empty($validated['favicon']) ? basename($validated['favicon']) : null,
                'site_title'         => $validated['site_title'],
                'footer_text'        => $validated['footer_text'],
                'footer_description' => $validated['footer_description'],
            ];

            foreach ($settings as $key => $value) {
                PhotoStudioSetup::updateOrCreate(
                    ['key' => $key, 'created_by' => creatorId()],
                    ['value' => $value, 'creator_id' => Auth::id(), 'created_by' => creatorId()]
                );
            }

            return redirect()->back()->with('success', __('The brand setting details have been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Dashboard Welcome Card
    public function dashboardWelcomeCardStore(StoreDashboardWelcomeCardRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-brand-settings')) {
            $validated = $request->validated();

            $settings = [
                'copy_link_card_title'       => $validated['copy_link_card_title'],
                'copy_link_card_description' => $validated['copy_link_card_description'],
                'copy_link_button_text'      => $validated['copy_link_button_text'],
                'copy_link_button_icon'      => $validated['copy_link_button_icon'],
            ];

            foreach ($settings as $key => $value) {
                PhotoStudioSetup::updateOrCreate(
                    ['key' => $key, 'created_by' => creatorId()],
                    ['value' => $value, 'creator_id' => Auth::id(), 'created_by' => creatorId()]
                );
            }

            return redirect()->back()->with('success', __('The dashboard welcome card settings have been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Testimonials
    public function testimonialsIndex()
    {
        if (Auth::user()->can('manage-photo-studio-testimonials')) {
            $testimonialsData = PhotoStudioSetup::where('created_by', creatorId())
                ->where('key', 'testimonials')
                ->first();

            $testimonials = [];
            $testimonial_title       = '';
            $testimonial_image       = '';
            $client_feedback_label   = '';
            $client_feedback_title   = '';

            if ($testimonialsData && $testimonialsData->value) {
                $decoded = json_decode($testimonialsData->value, true);
                $testimonials          = $decoded['testimonials'] ?? [];
                $testimonial_title     = $decoded['testimonial_title'] ?? '';
                $testimonial_image     = $decoded['testimonial_image'] ?? '';
                $client_feedback_label = $decoded['client_feedback_label'] ?? '';
                $client_feedback_title = $decoded['client_feedback_title'] ?? '';
            }

            if (empty($testimonials)) {
                $testimonials = [[
                    'customer_name' => '',
                    'designation'   => '',
                    'rating'        => 5,
                    'comment'       => '',
                    'profile_image' => null,
                ]];
            }

            return Inertia::render('PhotoStudioManagement/SystemSetup/testimonials', [
                'testimonials'          => $testimonials,
                'testimonial_title'     => $testimonial_title,
                'testimonial_image'     => $testimonial_image,
                'client_feedback_label' => $client_feedback_label,
                'client_feedback_title' => $client_feedback_title,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function testimonialsStore(StoreTestimonialRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-testimonials')) {
            $validated = $request->validated();

            if (!empty($validated['testimonial_image'])) {
                $validated['testimonial_image'] = basename($validated['testimonial_image']);
            }

            if (!empty($validated['testimonials']) && is_array($validated['testimonials'])) {
                foreach ($validated['testimonials'] as $key => $testimonial) {
                    if (!empty($testimonial['profile_image'])) {
                        $validated['testimonials'][$key]['profile_image'] = basename($testimonial['profile_image']);
                    }
                }
            }

            PhotoStudioSetup::updateOrCreate(
                ['key' => 'testimonials', 'created_by' => creatorId()],
                ['value' => json_encode($validated), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The testimonials have been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // FAQ Section
    public function faqIndex()
    {
        if (Auth::user()->can('manage-photo-studio-faqs')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/faqs', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function faqStore(StoreFaqSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-faqs')) {
            PhotoStudioSetup::updateOrCreate(
                ['key' => 'faq_section', 'created_by' => creatorId()],
                ['value' => json_encode($request->validated()), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The FAQ section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Media Section
    public function mediaIndex()
    {
        if (Auth::user()->can('manage-photo-studio-media-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/media-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function mediaStore(StoreMediaSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-media-section')) {
            $validated = $request->validated();

            if (!empty($validated['media_items']) && is_array($validated['media_items'])) {
                foreach ($validated['media_items'] as $key => $item) {
                    if (!empty($item['media_image'])) {
                        $validated['media_items'][$key]['media_image'] = basename($item['media_image']);
                    }
                }
            }

            PhotoStudioSetup::updateOrCreate(
                ['key' => 'media_section', 'created_by' => creatorId()],
                ['value' => json_encode($validated), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The media section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Award Section
    public function awardIndex()
    {
        if (Auth::user()->can('manage-photo-studio-award-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/award-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function awardStore(StoreAwardSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-award-section')) {
            PhotoStudioSetup::updateOrCreate(
                ['key' => 'award_section', 'created_by' => creatorId()],
                ['value' => json_encode($request->validated()), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The award section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Gallery Section
    public function galleryIndex()
    {
        if (Auth::user()->can('manage-photo-studio-gallery-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();
            $galleryTypes = PhotoStudioGalleryType::where('created_by', creatorId())
                ->where('status', true)
                ->get()
                ->map(fn($t) => ['value' => (string) $t->id, 'label' => $t->name])
                ->values()
                ->toArray();

            return Inertia::render('PhotoStudioManagement/SystemSetup/gallery-section', [
                'photostudiosetups' => $photostudiosetups,
                'galleryTypes'      => $galleryTypes,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function galleryStore(StoreGallerySectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-gallery-section')) {
            $validated = $request->validated();

            if (!empty($validated['images']) && is_array($validated['images'])) {
                foreach ($validated['images'] as $key => $imageData) {
                    if (!empty($imageData['image'])) {
                        $validated['images'][$key]['image'] = basename($imageData['image']);
                    }
                }
            }

            PhotoStudioSetup::updateOrCreate(
                ['key' => 'gallery_section', 'created_by' => creatorId()],
                ['value' => json_encode($validated), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The gallery section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Title Section
    public function titleIndex()
    {
        if (Auth::user()->can('manage-photo-studio-title-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/title-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function titleStore(StoreTitleSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-title-section')) {
            PhotoStudioSetup::updateOrCreate(
                ['key' => 'title_section', 'created_by' => creatorId()],
                ['value' => json_encode($request->validated()), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The title section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // About Section
    public function aboutIndex()
    {
        if (Auth::user()->can('manage-photo-studio-about-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/about-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function aboutStore(StoreAboutSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-about-section')) {
            $validated = $request->validated();

            if (!empty($validated['about_us_image'])) {
                $validated['about_us_image'] = basename($validated['about_us_image']);
            }

            PhotoStudioSetup::updateOrCreate(
                ['key' => 'about_section', 'created_by' => creatorId()],
                ['value' => json_encode($validated), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The about section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Contact Section
    public function contactIndex()
    {
        if (Auth::user()->can('manage-photo-studio-contact-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/contact-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function contactStore(StoreContactSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-contact-section')) {
            PhotoStudioSetup::updateOrCreate(
                ['key' => 'contact_section', 'created_by' => creatorId()],
                ['value' => json_encode($request->validated()), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The contact section has been saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Footer Section
    public function footerIndex()
    {
        if (Auth::user()->can('manage-photo-studio-footer-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/footer-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function footerStore(StoreFooterSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-footer-section')) {
            PhotoStudioSetup::updateOrCreate(
                ['key' => 'footer_section', 'created_by' => creatorId()],
                ['value' => json_encode($request->validated()), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The footer section has been saved successfully.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Banner Section
    public function bannerIndex()
    {
        if (Auth::user()->can('manage-photo-studio-banner-section')) {
            $photostudiosetups = PhotoStudioSetup::where('created_by', creatorId())->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/banner-section', [
                'photostudiosetups' => $photostudiosetups,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function bannerStore(StoreBannerSectionRequest $request)
    {
        if (Auth::user()->can('edit-photo-studio-banner-section')) {
            $validated = $request->validated();

            if (!empty($validated['banners']) && is_array($validated['banners'])) {
                foreach ($validated['banners'] as $key => $banner) {
                    if (!empty($banner['image'])) {
                        $validated['banners'][$key]['image'] = basename($banner['image']);
                    }
                }
            }

            PhotoStudioSetup::updateOrCreate(
                ['key' => 'banner_section', 'created_by' => creatorId()],
                ['value' => json_encode($validated), 'creator_id' => Auth::id(), 'created_by' => creatorId()]
            );

            return redirect()->back()->with('success', __('The banner section has been saved successfully.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
