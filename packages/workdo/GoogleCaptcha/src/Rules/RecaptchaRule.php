<?php

namespace Workdo\GoogleCaptcha\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        $secretKey = admin_setting('recaptcha_secret_key');
        if (empty($secretKey)) {
            $fail('reCAPTCHA is not properly configured.');
            return;
        }

        try {
            $response = Http::timeout(8)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (! $response->successful()) {
                Log::warning('recaptcha_http_error', [
                    'status' => $response->status(),
                ]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            $result = $response->json();
            $success = is_array($result) ? ($result['success'] ?? false) : false;

            if (! $success) {
                Log::warning('recaptcha_verification_failed', [
                    'error_codes' => is_array($result) ? ($result['error-codes'] ?? []) : [],
                    'hostname' => is_array($result) ? ($result['hostname'] ?? null) : null,
                ]);
                $fail('reCAPTCHA verification failed. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::warning('recaptcha_exception', [
                'exception' => get_class($e),
            ]);
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
