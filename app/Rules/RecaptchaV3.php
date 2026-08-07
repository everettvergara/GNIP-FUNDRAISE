<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class RecaptchaV3 implements ValidationRule
{
    public function __construct(
        private readonly string $action,
    ) {}

    /**
     * @return array<int, string|self>
     */
    public static function rules(string $action): array
    {
        if (! self::isEnabled()) {
            return ['nullable'];
        }

        return ['required', new self($action)];
    }

    public static function isEnabled(): bool
    {
        if (config('services.recaptcha.skip_verify')) {
            return false;
        }

        return (bool) config('services.recaptcha.enabled');
    }

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isEnabled()) {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('reCAPTCHA verification failed. Please try again.');

            return;
        }

        $secretKey = config('services.recaptcha.secret_key');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->successful()) {
            $fail('reCAPTCHA verification failed. Please try again.');

            return;
        }

        $result = $response->json();

        if (! ($result['success'] ?? false)) {
            $fail('reCAPTCHA verification failed. Please try again.');

            return;
        }

        if (($result['action'] ?? '') !== $this->action) {
            $fail('reCAPTCHA verification failed. Please try again.');

            return;
        }

        $minScore = config('services.recaptcha.min_score', 0.5);

        if (($result['score'] ?? 0) < $minScore) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
