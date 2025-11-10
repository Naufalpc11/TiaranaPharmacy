<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Throwable;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message.
     */
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        try {
            ContactMessage::create($request->validated());

            RateLimiter::hit($this->throttleKey($request), $this->decaySeconds());

            return back()->with('contact_submitted', true);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('contact_error', 'Terjadi kesalahan pada sistem. Silakan coba lagi beberapa saat lagi.');
        }
    }

    /**
     * Ensure the requester is not exceeding the allowed rate limits.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(StoreContactMessageRequest $request): void
    {
        $key = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'form' => sprintf(
                'Permintaan terlalu sering. Coba lagi dalam %s detik.',
                $seconds
            ),
        ]);
    }

    protected function throttleKey(StoreContactMessageRequest $request): string
    {
        $email = strtolower((string) $request->input('email', ''));

        return sprintf('contact-form|%s|%s', $email, $request->ip());
    }

    protected function maxAttempts(): int
    {
        return 3;
    }

    protected function decaySeconds(): int
    {
        return 5 * 60;
    }
}
