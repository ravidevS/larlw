<?php

use App\Jobs\SendBulkEmailJob;
use App\Mail\BulkAnnouncement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('sends bulk emails with attachments stored on the local disk and deletes temp files', function () {
    Mail::fake();
    Storage::fake('local');

    $users = User::factory()->count(2)->create();
    $attachmentPath = 'temp-attachments/announcement.pdf';

    Storage::disk('local')->put($attachmentPath, 'dummy-pdf-content');

    (new SendBulkEmailJob(
        subject: 'Important update',
        message: 'Please find the attachment.',
        attachmentPaths: [$attachmentPath]
    ))->handle();

    Mail::assertSent(BulkAnnouncement::class, 2);
    Mail::assertSent(BulkAnnouncement::class, fn (BulkAnnouncement $mail) => $mail->hasTo($users[0]->email));
    Mail::assertSent(BulkAnnouncement::class, fn (BulkAnnouncement $mail) => $mail->hasTo($users[1]->email));

    expect(Storage::disk('local')->exists($attachmentPath))->toBeFalse();
});
