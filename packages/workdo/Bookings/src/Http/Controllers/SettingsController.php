<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingSetting;
use Workdo\Bookings\Models\BookingCustomPage;

class SettingsController extends Controller
{
    public function brandSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-brand-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $header = $configData['general']['header'] ?? [];
            $footer = $configData['general']['footer'] ?? [];

            $customPages = BookingCustomPage::where('is_active', true)->get(['id', 'title', 'slug']);

            return Inertia::render('Bookings/SystemSetup/BrandSettings/Index', [
                'settings' => [
                    'header_logo' => $header['logo'] ?? '',
                    'footer_logo' => $footer['logo'] ?? '',
                    'favicon' => $header['favicon'] ?? '',
                    'site_title' => $header['site_title'] ?? '',
                    'default_language' => $header['default_language'] ?? 'en',
                    'show_language_selector' => $header['show_language_selector'] ?? true,
                    'navigation_items' => $header['navigation_items'] ?? [],
                    'footer_description' => $footer['description'] ?? '',
                    'footer_address' => $footer['address'] ?? '',
                    'footer_phone' => $footer['phone'] ?? '',
                    'footer_email' => $footer['email'] ?? '',
                    'footer_hours' => $footer['hours'] ?? '',
                    'footer_navigation_sections' => $footer['navigation_sections'] ?? [],
                    'footer_copyright' => $footer['copyright'] ?? '',
                ],
                'custom_pages' => $customPages
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function brandSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-brand-settings')) {
            $validated = $request->validate([
                'header_logo' => 'nullable|string',
                'footer_logo' => 'nullable|string',
                'favicon' => 'nullable|string',
                'site_title' => 'nullable|string|max:255',
                'default_language' => 'nullable|string|max:10',
                'show_language_selector' => 'nullable|boolean',
                'navigation_items' => 'nullable|array',
                'footer_description' => 'nullable|string',
                'footer_address' => 'nullable|string',
                'footer_phone' => 'nullable|string',
                'footer_email' => 'nullable|string',
                'footer_hours' => 'nullable|string',
                'footer_navigation_sections' => 'nullable|array',
                'footer_copyright' => 'nullable|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['general']['header']['logo'] = !empty($validated['header_logo']) ? basename($validated['header_logo']) : '';
            $configData['general']['header']['favicon'] = !empty($validated['favicon']) ? basename($validated['favicon']) : '';
            $configData['general']['header']['site_title'] = $validated['site_title'] ?? '';
            $configData['general']['header']['default_language'] = $validated['default_language'] ?? 'en';
            $configData['general']['header']['show_language_selector'] = $validated['show_language_selector'] ?? true;
            $configData['general']['header']['navigation_items'] = $validated['navigation_items'] ?? [];
            
            $configData['general']['footer']['logo'] = !empty($validated['footer_logo']) ? basename($validated['footer_logo']) : '';
            $configData['general']['footer']['description'] = $validated['footer_description'] ?? '';
            $configData['general']['footer']['address'] = $validated['footer_address'] ?? '';
            $configData['general']['footer']['phone'] = $validated['footer_phone'] ?? '';
            $configData['general']['footer']['email'] = $validated['footer_email'] ?? '';
            $configData['general']['footer']['hours'] = $validated['footer_hours'] ?? '';
            $configData['general']['footer']['navigation_sections'] = $validated['footer_navigation_sections'] ?? [];
            $configData['general']['footer']['copyright'] = $validated['footer_copyright'] ?? '';

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The brand settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function pageSectionsIndex()
    {
        if (Auth::user()->can('manage-booking-page-sections')) {
            $settings = BookingSetting::getSettings();
            
            return Inertia::render('Bookings/SystemSetup/PageSections/Index', [
                'settings' => $settings,
                'config_data' => $settings->config_data ?? []
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function pageSectionsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-page-sections')) {
            $request->validate([
                'config_data' => 'nullable|array'
            ]);

            $settings = BookingSetting::getSettings();
            $settings->update(['config_data' => $request->config_data]);

            return back()->with('success', __('The page sections are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function bannerSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-banner-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $banner = $configData['pages']['home']['banner'] ?? [];

            return Inertia::render('Bookings/SystemSetup/BannerSettings/Index', [
                'settings' => [
                    'title' => $banner['title'] ?? '',
                    'description' => $banner['description'] ?? '',
                    'banner_image' => $banner['image'] ?? '',
                ]
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function bannerSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-banner-settings')) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'banner_image' => 'nullable|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['pages']['home']['banner']['title'] = $validated['title'];
            $configData['pages']['home']['banner']['description'] = $validated['description'];
            $configData['pages']['home']['banner']['image'] = !empty($validated['banner_image']) ? basename($validated['banner_image']) : '';

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The banner settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function appointmentSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-appointment-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $booking = $configData['pages']['home']['booking'] ?? [];

            return Inertia::render('Bookings/SystemSetup/AppointmentSettings/Index', [
                'settings' => [
                    'title' => $booking['title'] ?? '',
                    'description' => $booking['description'] ?? '',
                    'why_book_with_us' => array_map(fn($f) => $f['text'] ?? '', $booking['features'] ?? []),
                ]
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function appointmentSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-appointment-settings')) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'why_book_with_us' => 'required|array|min:1',
                'why_book_with_us.*' => 'required|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['pages']['home']['booking']['title'] = $validated['title'];
            $configData['pages']['home']['booking']['description'] = $validated['description'];
            $configData['pages']['home']['booking']['features'] = array_map(fn($text) => ['text' => $text], $validated['why_book_with_us']);

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The appointment settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function additionalSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-additional-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $stats = $configData['pages']['home']['stats'] ?? [];
            $services = $configData['pages']['home']['services'] ?? [];
            $servicesPage = $configData['pages']['services']['header'] ?? [];
            $serviceDetailPage = $configData['pages']['service_detail']['header'] ?? [];

            return Inertia::render('Bookings/SystemSetup/AdditionalSettings/Index', [
                'settings' => [
                    'stats' => [
                        'title' => $stats['title'] ?? 'Trusted by Thousands Worldwide',
                        'description' => $stats['description'] ?? 'See why our booking solution has become the industry standard for service businesses'
                    ],
                    'services' => [
                        'title' => $services['title'] ?? 'Explore Our Premium Services',
                        'description' => $services['description'] ?? 'Discover the wide range of services that can be booked using our powerful addon solution'
                    ],
                    'service_detail' => [
                        'title' => $servicesPage['title'] ?? 'Our Services',
                        'description' => $servicesPage['description'] ?? 'Discover our comprehensive range of professional services designed to meet your needs'
                    ],
                    'related_services' => [
                        'title' => $serviceDetailPage['title'] ?? 'Service Details',
                        'description' => $serviceDetailPage['description'] ?? 'Complete information about our professional services'
                    ]
                ]
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function additionalSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-additional-settings')) {
            $validated = $request->validate([
                'stats.title' => 'required|string|max:255',
                'stats.description' => 'required|string',
                'services.title' => 'required|string|max:255',
                'services.description' => 'required|string',
                'service_detail.title' => 'required|string|max:255',
                'service_detail.description' => 'required|string',
                'related_services.title' => 'required|string|max:255',
                'related_services.description' => 'required|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['pages']['home']['stats']['title'] = $validated['stats']['title'];
            $configData['pages']['home']['stats']['description'] = $validated['stats']['description'];
            $configData['pages']['home']['services']['title'] = $validated['services']['title'];
            $configData['pages']['home']['services']['description'] = $validated['services']['description'];
            $configData['pages']['services']['header']['title'] = $validated['service_detail']['title'];
            $configData['pages']['services']['header']['description'] = $validated['service_detail']['description'];
            $configData['pages']['service_detail']['header']['title'] = $validated['related_services']['title'];
            $configData['pages']['service_detail']['header']['description'] = $validated['related_services']['description'];

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The additional settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function contactSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-contact-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $header = $configData['pages']['contact']['header'] ?? [];
            $map = $configData['pages']['contact']['map'] ?? [];

            return Inertia::render('Bookings/SystemSetup/ContactSettings/Index', [
                'settings' => [
                    'title' => $header['title'] ?? '',
                    'description' => $header['description'] ?? '',
                    'google_map_iframe' => $map['embed_code'] ?? '',
                ]
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function contactSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-contact-settings')) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'google_map_iframe' => 'nullable|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['pages']['contact']['header']['title'] = $validated['title'];
            $configData['pages']['contact']['header']['description'] = $validated['description'];
            $configData['pages']['contact']['map']['embed_code'] = $validated['google_map_iframe'] ?? '';

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The contact settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function aboutUsSettingsIndex()
    {
        if (Auth::user()->can('manage-booking-about-us-settings')) {
            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];
            $header = $configData['pages']['about']['header'] ?? [];
            $story = $configData['pages']['about']['story'] ?? [];
            $mission = $configData['pages']['about']['mission'] ?? [];
            $team = $configData['pages']['about']['team'] ?? [];

            return Inertia::render('Bookings/SystemSetup/AboutUsSettings/Index', [
                'settings' => [
                    'header_title' => $header['title'] ?? '',
                    'header_description' => $header['description'] ?? '',
                    'banner_image' => $story['image'] ?? '',
                    'title' => $story['title'] ?? '',
                    'description' => isset($story['content'][0]['content']) ? $story['content'][0]['content'] : '',
                    'mission_title' => $mission['title'] ?? '',
                    'mission_subtitle' => $mission['subtitle'] ?? '',
                    'mission_content_title' => $mission['content_title'] ?? '',
                    'mission_content_description' => $mission['content_description'] ?? '',
                    'mission_features' => $mission['features'] ?? [],
                    'team_title' => $team['title'] ?? '',
                    'team_subtitle' => $team['subtitle'] ?? '',
                    'team_members' => $team['members'] ?? [],
                ]
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function aboutUsSettingsUpdate(Request $request)
    {
        if (Auth::user()->can('edit-booking-about-us-settings')) {
            $validated = $request->validate([
                'header_title' => 'required|string|max:255',
                'header_description' => 'required|string',
                'banner_image' => 'nullable|string',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'mission_title' => 'required|string|max:255',
                'mission_subtitle' => 'required|string|max:255',
                'mission_content_title' => 'required|string|max:255',
                'mission_content_description' => 'required|string',
                'mission_features' => 'required|array|min:1',
                'mission_features.*.icon' => 'required|string',
                'mission_features.*.title' => 'required|string',
                'mission_features.*.description' => 'required|string',
                'team_title' => 'required|string|max:255',
                'team_subtitle' => 'required|string|max:255',
                'team_members' => 'required|array|min:1',
                'team_members.*.image' => 'required|string',
                'team_members.*.name' => 'required|string',
                'team_members.*.position' => 'required|string',
                'team_members.*.description' => 'required|string',
            ]);

            $settings = BookingSetting::getSettings();
            $configData = $settings->config_data ?? [];

            $configData['pages']['about']['header']['title'] = $validated['header_title'];
            $configData['pages']['about']['header']['description'] = $validated['header_description'];
            $configData['pages']['about']['story']['title'] = $validated['title'];
            $configData['pages']['about']['story']['image'] = !empty($validated['banner_image']) ? basename($validated['banner_image']) : '';
            $configData['pages']['about']['story']['content'] = [['content' => $validated['description']]];
            $configData['pages']['about']['mission']['title'] = $validated['mission_title'];
            $configData['pages']['about']['mission']['subtitle'] = $validated['mission_subtitle'];
            $configData['pages']['about']['mission']['content_title'] = $validated['mission_content_title'];
            $configData['pages']['about']['mission']['content_description'] = $validated['mission_content_description'];
            $configData['pages']['about']['mission']['features'] = $validated['mission_features'];
            $configData['pages']['about']['team']['title'] = $validated['team_title'];
            $configData['pages']['about']['team']['subtitle'] = $validated['team_subtitle'];
            $configData['pages']['about']['team']['members'] = array_map(function($member) {
                return [
                    'image' => !empty($member['image']) ? basename($member['image']) : '',
                    'name' => $member['name'],
                    'position' => $member['position'],
                    'description' => $member['description']
                ];
            }, $validated['team_members']);

            $settings->update(['config_data' => $configData]);

            return redirect()->back()->with('success', __('The about us settings are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}