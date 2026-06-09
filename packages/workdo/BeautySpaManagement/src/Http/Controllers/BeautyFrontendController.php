<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;
use Workdo\BeautySpaManagement\Models\BeautySubscriber;
use Workdo\BeautySpaManagement\Models\BeautyCustomPage;
use Workdo\BeautySpaManagement\Models\BeautyContact;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautySubscriberRequest;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyContactRequest;
use Workdo\BeautySpaManagement\Http\Requests\StoreFrontendBookingRequest;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyReviewRequest;
use Workdo\BeautySpaManagement\Events\CreateBeautySubscriber;
use Workdo\BeautySpaManagement\Models\BeautyWorking;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Models\BeautyReview;
use Workdo\Hrm\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class BeautyFrontendController extends Controller
{
    private function getUserIdFromRequest(Request $request)
    {
        $userSlug = $request->route('userSlug');
        if ($userSlug) {
            $user = User::where('slug', $userSlug)->first();
            if ($user) {
                return $user->id;
            }
        }
        
          // For backend requests, use creatorId()
        return creatorId();
    }

    public function index(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');
        
        $services = BeautyService::where('created_by', $userId)
            ->orderBy('id', 'desc')
            ->get(['id', 'name', 'description', 'service_image', 'price']);
            
        $totalBookings = BeautyBooking::where('stage_id',2)->where('created_by', $userId)->count();
        $totalClients  = BeautyBooking::where('created_by', $userId)->count();
        
        $offers = BeautyServiceOffer::where('created_by', $userId)
            ->where('end_date', '>=', now()->toDateString())
            ->where('start_date', '<=', now()->addDays(30)->toDateString())
            ->get(['id', 'title', 'name', 'description', 'price', 'offer_price', 'discount','beauty_service_id']);

          // Get working hours
        $working      = BeautyWorking::where('created_by', $userId)->first();
        $workingHours = null;
        if ($working) {
            $days          = explode(',', $working->day_of_week);
            $formattedDays = array_map(function($day) {
                return substr(trim($day), 0, 3);
            }, $days);
            
            $dayRange = count($formattedDays) > 1
                ? $formattedDays[0] . '-' . end($formattedDays)
                :  ($formattedDays[0] ?? '');
            
            $opening = Carbon::createFromFormat('H:i:s', $working->opening_time)->format('g:i A');
            $closing = Carbon::createFromFormat('H:i:s', $working->closing_time)->format('g:i A');
            
            $workingHours = [
                'day_range'    => $dayRange,
                'opening_time' => $opening,
                'closing_time' => $closing
            ];
        }

        return Inertia::render('BeautySpaManagement/Frontend/Index', [
            'title'         => "Home | {$userSlug} | Beauty Spa Management",
            'services'      => $services,
            'totalBookings' => $totalBookings,
            'totalClients'  => $totalClients,
            'offers'        => $offers,
            'workingHours'  => $workingHours
        ]);
    }

    public function services(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');
        $search   = $request->get('search');
        $perPage  = $request->get('per_page', 9);
        
        $query = BeautyService::where('created_by', $userId);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $services = $query->paginate($perPage, ['id', 'name', 'description', 'service_image', 'price', 'time']);

        $serviceTypes = BeautyServiceType::where('created_by', $userId)->pluck('name');
        
        return Inertia::render('BeautySpaManagement/Frontend/Services', [
            'title'        => "Our Services | {$userSlug} | Beauty Spa Management",
            'services'     => $services,
            'search'       => $search,
            'userSlug'     => $userSlug,
            'serviceTypes' => $serviceTypes
        ]);
    }

    public function serviceDetail(Request $request, $userSlug, $service)
    {
        $userId = $this->getUserIdFromRequest($request);
        
        $serviceData = BeautyService::where('id', $service)
            ->where('created_by', $userId)
            ->firstOrFail();
            
        $reviews = BeautyReview::where('beauty_services_id', $service)
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $averageRating = $reviews->avg('rating') ?? 0;
        $reviewCount   = $reviews->count();
            
        $relatedServices = BeautyService::where('created_by', $userId)
            ->where('id', '!=', $service)
            ->take(6)
            ->get(['id', 'name', 'description', 'service_image', 'price']);
        
        return Inertia::render('BeautySpaManagement/Frontend/ServiceDetail', [
            'title'            => "{$serviceData->name} | {$userSlug} | Beauty Spa Management",
            'service'          => $serviceData,
            'reviews'          => $reviews,
            'averageRating'    => round($averageRating, 1),
            'reviewCount'      => $reviewCount,
            'related_services' => $relatedServices,
            'slug'             => $userSlug
        ]);
    }

    public function storeReview(StoreBeautyReviewRequest $request, $userSlug, $service)
    {
        $userId    = $this->getUserIdFromRequest($request);
        $validated = $request->validated();

        $beautyReview                     = new BeautyReview();
        $beautyReview->name               = $validated['name'];
        $beautyReview->email              = $validated['email'];
        $beautyReview->beauty_services_id = $service;
        $beautyReview->rating             = $validated['rating'];
        $beautyReview->review             = $validated['review'];
        $beautyReview->creator_id         = null;
        $beautyReview->created_by         = $userId;
        $beautyReview->save();

        return back()->with('success', __('Review submitted successfully!'));

    }

    public function booking(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');
        
        $today    = Carbon::today();

        $services = BeautyService::with(['offers' => function ($q) use ($today) {
                $q->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today);
            }])
            ->where('created_by', $userId)
            ->get()
            ->map(function ($service) {
                $offer = $service->offers->first();

                return [
                'id'                 => $service->id,
                'name'               => $service->name,
                'time'               => $service->time,
                'price'              => $offer ? $offer->offer_price : $service->price,
                'offer_note'         => $offer ? "Offer: {$offer->title} - {$offer->discount}% off" : null,
                ];
            });

        return Inertia::render('BeautySpaManagement/Frontend/Booking', [
            'title'      => "Book Appointment | {$userSlug} | Beauty Spa Management",
            'services'   => $services,
            'service_id' => $request->get('service')
        ]);
    }

    public function contact(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');

        return Inertia::render('BeautySpaManagement/Frontend/Contact', [
            'title' => "Contact Us | {$userSlug} | Beauty Spa Management"
        ]);
    }
    public function contactStore(StoreBeautyContactRequest $request)
    {
        try {
            $userId    = $this->getUserIdFromRequest($request);
            $validated = $request->validated();

            $beautyContact             = new BeautyContact();
            $beautyContact->name       = $validated['name'];
            $beautyContact->email      = $validated['email'];
            $beautyContact->phone      = $validated['phone'];
            $beautyContact->subject    = $validated['subject'];
            $beautyContact->message    = $validated['message'];
            $beautyContact->created_by = $userId;
            $beautyContact->save();

            return back()->with('success', __('Contact message sent successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to send message. Please try again.'));
        }
    }
    public function about(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');

          // Get statistics data
        $statistics = [
            'services_count'     => BeautyService::where('created_by', $userId)->count(),
            'total_bookings'     => BeautyBooking::where('created_by', $userId)->count(),
            'pending_bookings'   => BeautyBooking::where('created_by', $userId)->where('stage_id', 0)->count(),
            'completed_bookings' => BeautyBooking::where('created_by', $userId)->where('stage_id', 2)->count()
        ];

        return Inertia::render('BeautySpaManagement/Frontend/About', [
            'title'      => "About Us | {$userSlug} | Beauty Spa Management",
            'statistics' => $statistics
        ]);
    }

    public function customPage(Request $request,$userSlug,$slug)
    {
        $userId = $this->getUserIdFromRequest($request);

        $customPage = BeautyCustomPage::where('slug', $slug)->where('created_by', $userId)
            ->firstOrFail();

        return Inertia::render('BeautySpaManagement/Frontend/CustomPage', [
            'title'      => "{$customPage->title} | {$userSlug} | Beauty Spa Management",
            'customPage' => $customPage
        ]);
    }

    public function subscribe(StoreBeautySubscriberRequest $request)
    {
        $userId    = $this->getUserIdFromRequest($request);
        $validated = $request->validated();

        $beautySubscriber = BeautySubscriber::updateOrCreate(
            ['email' => $validated['email'], 'created_by' => $userId],
        );

        CreateBeautySubscriber::dispatch($request, $beautySubscriber);

        return back()->with('success', __('Subscriber successfully subscribed.'));
    }


    public function bookingStore(StoreFrontendBookingRequest $request)
    {
        $userId    = $this->getUserIdFromRequest($request);
        $validated = $request->validated();
        $service = BeautyService::where('id', $validated['service'])
            ->where('created_by', $userId)
            ->firstOrFail();
        $price   = $service->price;

        $offer = BeautyServiceOffer::where('beauty_service_id', $service->id)
            ->where('start_date', '<=', $validated['date'])
            ->where('end_date', '>=', $validated['date'])
            ->where('created_by', $userId)
            ->first();

        if ($offer) {
            $price = $offer->offer_price;
        }

        $servicePrice = BeautyBooking::total_amount($validated['person'], $price);

        $times        = explode('-', $validated['time_slot']);
        
        $booking                 = new BeautyBooking();
        $booking->name           = $validated['name'];
        $booking->email          = $validated['email'];
        $booking->phone_number   = $validated['phone_number'];
        $booking->service        = $validated['service'];
        $booking->date           = $validated['date'];
        $booking->start_time     = $times[0];
        $booking->end_time       = $times[1];
        $booking->person         = $validated['person'];
        $booking->price          = $servicePrice;
        $booking->gender         = $validated['gender'];
        $booking->reference      = $validated['reference'];
        $booking->notes          = $validated['additional_notes'];
        $booking->payment_option = 'Offline';
        $booking->stage_id       = 0;
        $booking->creator_id     = null;
        $booking->created_by     = $userId;
        $booking->save();

        return redirect()->route('beauty-spa.booking-success', ['userSlug' => $request->route('userSlug'), 'id' => Crypt::encrypt($booking->id)])
            ->with('success', __('Your booking request has been submitted successfully!'));
    }
    public function bookingSuccess(Request $request)
    {
        $userId   = $this->getUserIdFromRequest($request);
        $userSlug = $request->route('userSlug');
        
        $booking = null;
        if ($request->get('id')) {
            try {
                $bookingId = Crypt::decrypt($request->get('id'));
                $booking   = BeautyBooking::with('beautyService')->find($bookingId);
                  // Add service name directly to booking object
                if ($booking && $booking->beautyService) {
                    $booking->service_name = $booking->beautyService->name;
                }
            } catch (\Exception $e) {}
        }
        
        $setup       = BeautySetup::where('created_by', $userId)->pluck('value', 'key');
        $contactJson = json_decode($setup['contact_info'] ?? '{}', true);
        
        return Inertia::render('BeautySpaManagement/Frontend/BookingSuccess', [
            'title'         => "Booking Confirmed | {$userSlug} | Beauty Spa Management",
            'beautybooking' => $booking,
            'contact_info'  => [
                'beauty_spa_store_name' => $setup['beauty_spa_store_name'] ?? 'Beauty Spa',
                'phone_number'          => $contactJson['phone_number'] ?? ''
            ]
        ]);
    }
   
      // for generate slot
    private function generateTimeSlots($working, $service, $date, $userId)
    {
        $slots              = [];
        $startTime          = Carbon::parse($working->opening_time);
        $endTime            = Carbon::parse($working->closing_time);
        $serviceDuration    = $this->convertToMinutes($service->time ?? '1:00');
        $maxBookablePersons = $service->max_bookable_persons ?? 1;

        $currentSlot = $startTime->copy();
        
        while ($currentSlot->copy()->addMinutes($serviceDuration)->lte($endTime)) {
            $slotStart = $currentSlot->copy();
            $slotEnd   = $currentSlot->copy()->addMinutes($serviceDuration);
            
            $bookedPersons = BeautyBooking::where('service', $service->id)
                ->where('date', $date)
                ->where('start_time', $slotStart->format('H:i:s'))
                ->where('end_time', $slotEnd->format('H:i:s'))
                ->where('created_by', $userId)
                ->where('stage_id', '!=', 3)  // Exclude cancelled bookings
                ->sum('person');

            $availableSpots = $maxBookablePersons - $bookedPersons;

            if ($availableSpots > 0) {
                $slots[] = [
                    'start_time'      => $slotStart->format('H:i'),
                    'end_time'        => $slotEnd->format('H:i'),
                    'display'         => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'available_seats' => $availableSpots
                ];
            }
            
            $currentSlot->addMinutes($serviceDuration);
        }

        return $slots;
    }

    private function convertToMinutes($time)
    {
        if (is_numeric($time)) {
              // Handle decimal format (1.30 = 1 hour 30 minutes)
            $hours   = floor($time);
            $minutes = ($time - $hours) * 100;
            return ($hours * 60) + $minutes;
        }
        
          // Handle H:i format
        if (strpos($time, ':') !== false) {
            list($hours, $minutes) = explode(':', $time);
            return ($hours * 60) + $minutes;
        }
        
        return 60;  // Default 60 minutes
    }
      // for getting service price
    public function getServicePrice(Request $request)
    {
        $service = BeautyService::find($request->service_id);
        if (!$service) {
            return response()->json(['error' => 'Service not found']);
        }

        $price     = $service->price;
        $offerNote = null;
        
        if ($request->date) {
            $offer = BeautyServiceOffer::where('beauty_service_id', $service->id)
                ->where('start_date', '<=', $request->date)
                ->where('end_date', '>=', $request->date)
                ->first();
            
            if ($offer) {
                $price     = $offer->offer_price;
                $offerNote = "Offer: {$offer->title} - {$offer->discount}% off";
            }
        }

        return response()->json([
            'formatted_price' => $price,
            'offer_note'      => $offerNote
        ]);
    }

    public function checkHoliday(Request $request)
    {
        try {
            $request->validate([
                'service' => 'required|integer',
                'date'    => 'required|date'
            ]);
            
            $userId = $this->getUserIdFromRequest($request);
            
            $service = BeautyService::where('id', $request->service)
                ->where('created_by', $userId)
                ->first();
                
            if (!$service) {
                return response()->json(['is_success' => false, 'message' => 'Service not found']);
            }

            $selectedDate = Carbon::parse($request->date);
            $dayName      = strtolower($selectedDate->format('l'));
            
            $working = BeautyWorking::where('created_by', $userId)
                ->where('day_of_week', 'like', '%' . ucfirst($dayName) . '%')
                ->first();
                
                if (!$working || strpos($working->day_of_week, ucfirst($dayName)) === false) {
                return response()->json([
                    'is_success' => false,
                    'message'    => __('Selected date is a day off week. Please choose another date.')
                ]);
            }
            if ( $working->holiday_setting == 'on' && module_is_active('Hrm',$userId)) {
                $formattedSelectedDate = $selectedDate->format('Y-m-d');
                $holidays              = Holiday::where('created_by', $userId)
                    ->where('start_date', '<=', $formattedSelectedDate)
                    ->where('end_date', '>=', $formattedSelectedDate)
                    ->exists();

                if ($holidays) {
                    return response()->json([
                        'is_success' => false,
                        'message'    => __('Selected date is a holiday. Please choose another date.')
                    ]);
                }
            }

            $slots = $this->generateTimeSlots($working, $service, $request->date, $userId);
            
            return response()->json([
                'is_success' => true,
                'slots'      => $slots
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message'    => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function validateSlotCapacity(Request $request)
    {
        try {
            $userId = $this->getUserIdFromRequest($request);
            
            $service = BeautyService::where('id', $request->service)
                ->where('created_by', $userId)
                ->first();
                
            if (!$service) {
                return response()->json(['is_success' => false, 'message' => 'Service not found'], 200);
            }

            $times     = explode('-', $request->time_slot);
            $startTime = $times[0];
            $endTime   = $times[1];

            $bookedPersons = BeautyBooking::where('service', $service->id)
                ->where('date', $request->date)
                ->where('start_time', $startTime)
                ->where('end_time', $endTime)
                ->where('created_by', $userId)
                ->sum('person');

            $availableSpots   = ($service->max_bookable_persons ?? 1) - $bookedPersons;
            $requestedPersons = (int)$request->persons;

            if ($requestedPersons > $availableSpots) {
                return response()->json([
                    'is_success' => false,
                    'message'    => __('Only :available seats are available in this slot.', ['available' => $availableSpots])
                ]);
            }

            return response()->json(['is_success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message'    => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}