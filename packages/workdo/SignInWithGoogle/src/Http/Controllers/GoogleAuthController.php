<?php

namespace Workdo\SignInWithGoogle\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Workdo\SignInWithGoogle\Models\GoogleUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // Check if Sign-In With Google is enabled
        $isEnabled = admin_setting('google_signin_enabled');
        if ($isEnabled !== 'on') {
            return redirect()->route('login')->with('error', __('Sign-In With Google is not enabled.'));
        }
        
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if Google user already exists
            $existingGoogleUser = GoogleUser::where('google_id', $googleUser->getId())->first();

            if ($existingGoogleUser) {
                $user = $existingGoogleUser->user;
                Auth::login($user);
                return redirect()->route('dashboard');
            }
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Link existing user with Google account
                GoogleUser::create([
                    'google_id' => $googleUser->getId(),
                    'user_id' => $user->id,
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar() ?? 'avatar.png',
                ]);
                
                Auth::login($user);
                
                return redirect()->route('dashboard');
            } else {
                // Check if registration is enabled
                $enableRegistration = admin_setting('enableRegistration');

                if ($enableRegistration !== 'on') {
                    return redirect()->route('login');
                }
                
                $enableEmailVerification = admin_setting('enableEmailVerification');

                $adminUser = User::where('type', 'superadmin')->first();
                // Create new user
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make('1234'),
                    'email_verified_at' => $enableEmailVerification === 'on' ? null : now(),
                    'type' => 'company',
                    'lang' => $adminUser->lang,
                    'avatar' => $googleUser->getAvatar() ?? 'avatar.png', 
                    'created_by' => $adminUser ? $adminUser->id : null,
                ]);
                
                // Create Google user record
                GoogleUser::create([
                    'google_id' => $googleUser->getId(),
                    'user_id' => $newUser->id,
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar() ?? 'avatar.png',
                ]);
                
                User::CompanySetting($newUser->id);
                User::MakeRole($newUser->id);
                $newUser->assignRole($newUser->type);
                Auth::login($newUser);

                // Send welcome email
                if (admin_setting('New User') == 'on') {
                    $emailData = [
                        'name'     => $newUser->name,
                        'email'    => $newUser->email,
                        'password' => 1234,
                    ];

                    EmailTemplate::sendEmailTemplate('New User', [$newUser->email], $emailData, $adminUser->id);
                }

                if ($enableEmailVerification === 'on') {
                    // Apply dynamic mail configuration
                    SetConfigEmail($adminUser->id);
                    $newUser->sendEmailVerificationNotification();
                    return redirect(route('verification.notice'))->with('status', 'verification-link-sent');
                }
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('Google authentication failed. Please try again.'));
        }
    }
}