<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendBulkEmailRequest;
use App\Jobs\SendBulkEmailJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BulkEmailController extends Controller
{
    /**
     * Show the bulk email form.
     */
    public function index(): View
    {
        return view('bulk-emails.create');
    }

    /**
     * Handle the bulk email submission.
     */
    public function send(SendBulkEmailRequest $request): RedirectResponse
    {
        $attachmentPaths = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Save relative path
                $attachmentPaths[] = $file->store('temp-attachments');
            }
        }

        // Dispatch the job with relative paths
        SendBulkEmailJob::dispatch(
         $request->validated('subject'),
         $request->validated('message'),
         $attachmentPaths
        );

        return back()->with('status', 'Your bulk email has been queued successfully.');
    }
}
