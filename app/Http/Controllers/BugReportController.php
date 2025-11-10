<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBugReportRequest;
use App\Models\BugReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class BugReportController extends Controller
{
    /**
     * Store a newly submitted bug report.
     */
    public function store(StoreBugReportRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $validated = $request->validated();
        $screenshotPath = null;
        $screenshotOriginalName = null;

        try {
            if ($request->hasFile('screenshot')) {
                $file = $request->file('screenshot');
                $screenshotPath = $file->store('bug-reports', 'public');
                $screenshotOriginalName = $file->getClientOriginalName();
            }

            BugReport::create([
                'subject' => $validated['subject'],
                'email' => $validated['email'] ?? null,
                'description' => $validated['description'],
                'screenshot_path' => $screenshotPath,
                'screenshot_original_name' => $screenshotOriginalName,
                'status' => BugReport::STATUS_NEW,
            ]);

            RateLimiter::hit($this->throttleKey($request), $this->decaySeconds());

            return back()->with('bug_report_submitted', true);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            if ($screenshotPath) {
                Storage::disk('public')->delete($screenshotPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('bug_report_error', 'Terjadi kesalahan pada sistem. Silakan coba kembali nanti.');
        }
    }

    /**
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(StoreBugReportRequest $request): void
    {
        $key = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'form' => sprintf(
                'Anda telah mengirim terlalu banyak laporan. Coba lagi dalam %s detik.',
                $seconds
            ),
        ]);
    }

    protected function throttleKey(StoreBugReportRequest $request): string
    {
        $email = strtolower((string) $request->input('email', 'anon'));

        return sprintf('bug-report|%s|%s', $email, $request->ip());
    }

    protected function maxAttempts(): int
    {
        return 2;
    }

    protected function decaySeconds(): int
    {
        return 15 * 60;
    }
}
