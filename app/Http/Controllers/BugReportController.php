<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBugReportRequest;
use App\Models\BugReport;
use Illuminate\Http\RedirectResponse;

class BugReportController extends Controller
{
    /**
     * Store a newly submitted bug report.
     */
    public function store(StoreBugReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $screenshotPath = null;
        $screenshotOriginalName = null;

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
        ]);

        return back()->with('bug_report_submitted', true);
    }
}
