<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingCustomPage;
use Workdo\Bookings\Models\BookingSetting;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\Bookings\Models\BookingPackage;
use App\Models\User;
use Workdo\Bookings\Models\BookingDuration;
use Workdo\Bookings\Models\BookingAppointment;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingReview;
use Workdo\Bookings\Models\BookingStaff;
use Workdo\Bookings\Models\BookingSocialLink;
use Workdo\Bookings\Models\BookingBusinessHours;
use Workdo\Bookings\Models\BookingContact;

class BookingController extends Controller
{
    private function getFrontendData($userSlug)
    {
        $user = User::where('slug', $userSlug)->firstOrFail();
        $userId = $user->id;
        
        $settingsModel = BookingSetting::getSettings($userId);
        $configData = $settingsModel->config_data;
        
        $socialLinks = BookingSocialLink::where('created_by', $userId)->get();
        $customPages = BookingCustomPage::where('created_by', $userId)
            ->where('is_active', true)
            ->select('id', 'title', 'slug')
            ->get();
        
        $footerServices = ProductServiceItem::where('type', 'bookings')
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->select('id', 'name')
            ->get();
        
        return [
            'brandSettings' => [
                'logo' => $configData['general']['header']['logo'] ?? '',
                'footer_logo' => $configData['general']['footer']['logo'] ?? '',
                'favicon' => $configData['general']['header']['favicon'] ?? '',
                'site_title' => $configData['general']['header']['site_title'] ?? 'Booking Service',
                'show_language_selector' => $configData['general']['header']['show_language_selector'] ?? true,
                'default_language' => $configData['general']['header']['default_language'] ?? 'en',
                'footer_description' => $configData['general']['footer']['description'] ?? '',
                'footer_copyright' => $configData['general']['footer']['copyright'] ?? '',
                'footer_contact_title' => $configData['general']['footer']['contact_title'] ?? 'Contact Information',
                'footer_address' => $configData['general']['footer']['address'] ?? '',
                'footer_phone' => $configData['general']['footer']['phone'] ?? '',
                'footer_email' => $configData['general']['footer']['email'] ?? '',
                'footer_hours' => $configData['general']['footer']['hours'] ?? '',
                'userSlug' => $userSlug
            ],
            'colorSettings' => [
                'primary_color' => $configData['general']['colors']['primary_color'] ?? '#52816D',
                'secondary_color' => $configData['general']['colors']['secondary_color'] ?? '#ffffff'
            ],
            'bannerSettings' => [
                'title' => $configData['pages']['home']['banner']['title'] ?? '',
                'description' => $configData['pages']['home']['banner']['description'] ?? '',
                'image' => $configData['pages']['home']['banner']['image'] ?? ''
            ],
            'socialLinks' => $socialLinks,
            'customPages' => $customPages,
            'footerServices' => $footerServices,
            'pageSettings' => $configData['pages'] ?? [],
            'userSlug' => $userSlug
        ];
    }
    
    private function getUserIdFromRequest(Request $request)
    {        
        // Fallback to slug-based detection
        $userSlug = $request->route('userSlug');
        if ($userSlug) {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {
                return $user->id;
            }
        }
        
        abort(404, __('Booking page not found'));
    }
    


    public function home(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $userId = $this->getUserIdFromRequest($request);
        
        $items = ProductServiceItem::where('type', 'bookings')
            ->where('created_by', $userId)
            ->get(['id', 'name', 'description', 'image']);

        $packageItems = BookingPackage::where('created_by', $userId)
            ->get(['id', 'name', 'item_id', 'price']);

        $staff_ids = BookingStaff::where('created_by', $userId)->pluck('staff_id')->toArray();

        $users = User::whereIn('id', $staff_ids)
            ->where('type', '!=', 'superadmin')
            ->get(['id', 'name']);

        $closedDays = BookingBusinessHours::where('created_by', $userId)->where('is_closed', true)->pluck('day_of_week')->toArray();
        
        $settings = $this->getFrontendData($userSlug);
        
        return Inertia::render('Bookings/Frontend/Home', [
            'title' => __('Welcome to Our Booking Service'),
            'brandSettings' => $settings['brandSettings'],
            'colorSettings' => $settings['colorSettings'],
            'bannerSettings' => $settings['bannerSettings'],
            'socialLinks' => $settings['socialLinks'],
            'customPages' => $settings['customPages'],
            'footerServices' => $settings['footerServices'],
            'pageSettings' => $settings['pageSettings'],
            'items' => $items,
            'packageItems' => $packageItems,
            'users' => $users,
            'closedDays' => $closedDays,
            'userSlug' => $userSlug
        ]);
    }

    public function about(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $this->getUserIdFromRequest($request);
        $settings = $this->getFrontendData($userSlug);
        
        return Inertia::render('Bookings/Frontend/About', [
            'title' => __('About Us'),
            'brandSettings' => $settings['brandSettings'],
            'colorSettings' => $settings['colorSettings'],
            'bannerSettings' => $settings['bannerSettings'],
            'socialLinks' => $settings['socialLinks'],
            'customPages' => $settings['customPages'],
            'footerServices' => $settings['footerServices'],
            'pageSettings' => $settings['pageSettings'],
            'userSlug' => $userSlug
        ]);
    }

    public function contact(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $this->getUserIdFromRequest($request);
        $settings = $this->getFrontendData($userSlug);
        
        return Inertia::render('Bookings/Frontend/Contact', [
            'title' => __('Contact Us'),
            'brandSettings' => $settings['brandSettings'],
            'colorSettings' => $settings['colorSettings'],
            'bannerSettings' => $settings['bannerSettings'],
            'socialLinks' => $settings['socialLinks'],
            'customPages' => $settings['customPages'],
            'footerServices' => $settings['footerServices'],
            'pageSettings' => $settings['pageSettings'],
            'userSlug' => $userSlug
        ]);
    }

    public function services(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $userId = $this->getUserIdFromRequest($request);
        $search = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = 9;

        $query = ProductServiceItem::where('type', 'bookings')
            ->where('created_by', $userId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $items = $query->select(['id', 'name', 'description', 'image'])
            ->paginate($perPage, ['*'], 'page', $page);
        
        $settings = $this->getFrontendData($userSlug);

        return Inertia::render('Bookings/Frontend/Services', [
            'title' => __('Our Services'),
            'brandSettings' => $settings['brandSettings'],
            'colorSettings' => $settings['colorSettings'],
            'bannerSettings' => $settings['bannerSettings'],
            'socialLinks' => $settings['socialLinks'],
            'customPages' => $settings['customPages'],
            'footerServices' => $settings['footerServices'],
            'pageSettings' => $settings['pageSettings'],
            'items' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'total_pages' => $items->lastPage(),
                'total_items' => $items->total(),
                'per_page' => $items->perPage()
            ],
            'search' => $search,
            'userSlug' => $userSlug
        ]);
    }

    public function serviceDetail(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $userId = $this->getUserIdFromRequest($request);
        $id = $request->route('id');
        $item = ProductServiceItem::where('type', 'bookings')
            ->where('created_by', $userId)
            ->where('id', $id)
            ->first(['id', 'name', 'description', 'image']);

        if (!$item) {
            abort(404);
        }

        $duration = BookingDuration::where('item_id', $id)->first();
        $reviews = BookingReview::where('item_id', $id)
            ->orderBy('created_at', 'desc')
            ->get(['name', 'rating', 'comment', 'created_at']);
        
        $averageRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
        $totalReviews = $reviews->count();
        
        $settings = $this->getFrontendData($userSlug);

        return Inertia::render('Bookings/Frontend/ServiceDetail', [
            'title' => $item->name . ' - Service Details',
            'brandSettings' => $settings['brandSettings'],
            'colorSettings' => $settings['colorSettings'],
            'bannerSettings' => $settings['bannerSettings'],
            'socialLinks' => $settings['socialLinks'],
            'customPages' => $settings['customPages'],
            'footerServices' => $settings['footerServices'],
            'pageSettings' => $settings['pageSettings'],
            'serviceId' => $id,
            'item' => $item,
            'duration' => $duration,
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'totalReviews' => $totalReviews,
            'userSlug' => $userSlug
        ]);
    }

    public function notFound(Request $request)
    {
        try {
            $userSlug = $request->route('userSlug');
            $this->getUserIdFromRequest($request);
            $settings = $this->getFrontendData($userSlug);
            $notFoundSettings = $settings['page_settings']['pages']['notfound']['notfound'] ?? [];
            
            return Inertia::render('Bookings/Frontend/NotFound', [
                'title' => $notFoundSettings['title'] ?? __('Page Not Found'),
                'brandSettings' => $settings['brandSettings'],
                'colorSettings' => $settings['colorSettings'],
                'bannerSettings' => $settings['bannerSettings'],
                'socialLinks' => $settings['socialLinks'],
                'customPages' => $settings['customPages'],
                'footerServices' => $settings['footerServices'],
                'pageSettings' => $settings['pageSettings'],
                'notFoundSettings' => $notFoundSettings,
                'userSlug' => $userSlug
            ]);
        } catch (\Exception $e) {
            return response()->view('errors.404', [], 404);
        }
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000'
        ]);

        return back()->with('success', __('Thank you for your message. We will get back to you soon!'));
    }

    public function storeContact(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string'
        ]);

       BookingContact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
            'creator_id' => $userId,
            'created_by' => $userId
        ]);

        return back()->with('success', __('Thank you for contacting us! We will get back to you soon.'));
    }

    public function submitBooking(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'date' => 'required|date|after:today',
            'time' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20'
        ]);

        return back()->with('success', __('Your booking request has been submitted successfully!'));
    }

    public function submitReview(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'item_id' => 'required|integer'
        ]);

        $review = BookingReview::create([
            'name' => $request->name,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'item_id' => $request->item_id,
            'created_by' => $userId
        ]);

        return redirect()->back()->with('success', __('Thank you for your review!'));
    }

    public function store(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        $request->validate([
            'selectedDate' => 'required|date',
            'selectedStaff' => 'nullable|integer',
            'selectedItem' => 'required|integer', 
            'selectedPackage' => 'required|integer',
            'selectedTimeSlot' => 'required|array',
            'formData' => 'required|array',
            'formData.firstName' => 'required|string|max:255',
            'formData.lastName' => 'required|string|max:255',
            'formData.email' => 'required|email|max:255',
            'formData.phone' => 'required|string|max:20',
            'formData.paymentOption' => 'required|string'
        ]);

        // Find or create customer
        $customer = BookingCustomer::where('email', $request->formData['email'])
            ->where('created_by', $userId)
            ->first();

        if ($customer) {
            $customer->update([
                'first_name' => $request->formData['firstName'],
                'last_name' => $request->formData['lastName'],
                'mobile_number' => $request->formData['phone'],
                'description' => $request->formData['description'] ?? null,
            ]);
        } else {
            $customer = BookingCustomer::create([
                'first_name' => $request->formData['firstName'],
                'last_name' => $request->formData['lastName'],
                'email' => $request->formData['email'],
                'mobile_number' => $request->formData['phone'],
                'description' => $request->formData['description'] ?? null,
                'created_by' => $userId,
                'creator_id' => $userId,
            ]);
        }

        // Generate appointment number
        $currentYear = date('Y');
        $lastAppointment = BookingAppointment::where('created_by', $userId)
            ->where('appointment_number', 'like', 'APT-' . $currentYear . '-' . $userId . '-%')
            ->orderBy('appointment_number', 'desc')
            ->first();
        
        if ($lastAppointment) {
            $lastNumber = (int) substr($lastAppointment->appointment_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $appointmentNumber = 'APT-' . $currentYear . '-' . $userId . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Create appointment
        $appointment = BookingAppointment::create([
            'appointment_number' => $appointmentNumber,
            'date' => $request->selectedDate,
            'item_id' => $request->selectedItem,
            'package_id' => $request->selectedPackage,
            'staff_id' => $request->selectedStaff,
            'customer_id' => $customer->id,
            'start_time' => $request->selectedTimeSlot['start_time'],
            'end_time' => $request->selectedTimeSlot['end_time'],
            'payment' => $request->formData['paymentOption'] ?? 'offline',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_by' => $userId,
            'creator_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Appointment booked successfully!'),
            'appointment_id' => $appointment->id,
            'appointment_number' => $appointment->appointment_number
        ]);
    }

    public function customPage(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $userId = $this->getUserIdFromRequest($request);
        $slug = $request->route('slug');
        
        // Try to find the page with the exact slug first
        $page = BookingCustomPage::where('slug', $slug)
            ->where('is_active', true)
            ->where('created_by', $userId)
            ->first();
            
        // If not found, try with company-specific slug
        if (!$page) {
            $page = BookingCustomPage::where('slug', $slug . '-' . $userId)
                ->where('is_active', true)
                ->where('created_by', $userId)
                ->first();
        }
        
        if (!$page) {
            return $this->notFound($request);
        }
            
        $settings = BookingSetting::getSettings($userId);
        $userSlug = $request->route('userSlug');

        return Inertia::render('Bookings/Frontend/CustomPage', [
            'page' => $page,
            'settings' => $settings,
            'userSlug' => $userSlug,
            'bookingSettings' => $settings
        ]);
    }


    public function socialLinks(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        
        $socialLinks = BookingSocialLink::where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($socialLinks);
    }
}