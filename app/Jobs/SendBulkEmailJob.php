<?php

namespace App\Jobs;

use App\Mail\BulkAnnouncement;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBulkEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array<int, string>  $attachmentPaths
     */
    public function __construct(
        public string $subject,
        public string $message,
        public array $attachmentPaths = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $users = User::all();

        foreach ($users as $user) {

            Mail::to($user->email)->send(
                new BulkAnnouncement($this->subject, $this->message, $this->attachmentPaths)
            );
        }

        // Clean up temporary files
        foreach ($this->attachmentPaths as $path) {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }
    }
}
