<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\User;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSetup;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Models\PhotoStudioContact;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSubscriber;
use Workdo\PhotoStudioManagement\Http\Requests\StoreContactRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioContact;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioSubscriber;
class FrontendController extends Controller
{
    private function getUserIdFromRequest(Request $request): int
    {
        $user = User::where('slug', $request->route('userSlug'))->firstOrFail();
        return $user->id;
    }

    public function index(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);

        $services = PhotoStudioService::where('created_by', $userId)
            ->where('status', true)
            ->get()
            ->append('category_names');

        $cameraKits = PhotoStudioCameraKit::where('created_by', $userId)
            ->where('status', 'available')
            ->get()
            ->append('tag_names');

        return Inertia::render('PhotoStudioManagement/Frontend/Index', [
            'services'   => $services,
            'cameraKits' => $cameraKits,
        ]);
    }

    public function services(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);

        $services = PhotoStudioService::where('created_by', $userId)
            ->where('status', true)
            ->get()
            ->append('category_names');

        return Inertia::render('PhotoStudioManagement/Frontend/Services', [
            'services' => $services,
        ]);
    }

    public function portfolio(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        
        // PhotoStudio settings including gallery_section are already shared by middleware
        // Only pass gallery types for filtering
        $galleryTypes = PhotoStudioGalleryType::where('created_by', $userId)
            ->where('status', true)
            ->get(['id', 'name']);

        return Inertia::render('PhotoStudioManagement/Frontend/Portfolio', [
            'galleryTypes' => $galleryTypes,
        ]);
    }

    public function appointment(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);

        $services = PhotoStudioService::where('created_by', $userId)
            ->where('status', true)
            ->get(['id', 'name', 'price']);

        return Inertia::render('PhotoStudioManagement/Frontend/Appointment', [
            'services' => $services,
        ]);
    }

    public function faq(Request $request)
    {
        return Inertia::render('PhotoStudioManagement/Frontend/FAQ');
    }

    public function contact(Request $request)
    {
        return Inertia::render('PhotoStudioManagement/Frontend/Contact');
    }

    public function mediaAwards(Request $request)
    {
        return Inertia::render('PhotoStudioManagement/Frontend/MediaAwards');
    }

    public function cameraKit(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);

        $cameraKits = PhotoStudioCameraKit::where('created_by', $userId)
            ->where('status', 'available')
            ->with('equipmentType')
            ->get()
            ->append('tag_names');

        return Inertia::render('PhotoStudioManagement/Frontend/CameraKit', [
            'cameraKits' => $cameraKits,
            'equipmentTypes' => PhotoStudioEquipmentType::where('created_by', $userId)->where('status', true)->get(),
        ]);
    }

    public function storeNewsletter(Request $request)
    {
        $userId = $this->getUserIdFromRequest($request);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Check if email already exists
        $existingSubscription = PhotoStudioSubscriber::where('email', $validated['email'])
            ->where('created_by', $userId)
            ->first();

        if ($existingSubscription) {
            return redirect()->back()->with('success', __('Email already subscribed to newsletter!'));
        }

        // Store newsletter subscription
        $subscriber = new PhotoStudioSubscriber();
        $subscriber->email = $validated['email'];
        $subscriber->subscribed_date = now();
        $subscriber->creator_id = $userId;
        $subscriber->created_by = $userId;
        $subscriber->save();

        // Fire event
        CreatePhotoStudioSubscriber::dispatch($request, $subscriber);

        return redirect()->back()->with('success', __('Successfully subscribed to newsletter!'));
    }

    public function storeContact(StoreContactRequest $request)
    {
        $userId = $this->getUserIdFromRequest($request);
        $validated = $request->validated();

        // Store contact message
        $contact = new PhotoStudioContact();
        $contact->first_name = $validated['first_name'];
        $contact->last_name = $validated['last_name'];
        $contact->email = $validated['email'];
        $contact->phone_number = $validated['phone_number'] ?? null;
        $contact->message = $validated['message'];
        $contact->received_date = now();
        $contact->creator_id = $userId;
        $contact->created_by = $userId;
        $contact->save();

        // Fire event
        CreatePhotoStudioContact::dispatch($request, $contact);

        return redirect()->back()->with('success', __('Thank you for your message. We will get back to you soon!'));
    }

    public function customPage(Request $request, string $userSlug, string $slug)
    {
        $user = User::where('slug', $userSlug)->firstOrFail();
        $page = PhotoStudioCustomPage::where('created_by', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('PhotoStudioManagement/Frontend/CustomPage', [
            'page' => $page,
        ]);
    }
}
