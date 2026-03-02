<?php

use App\Jobs\SendBulkEmailJob;
use App\Mail\BulkAnnouncement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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

it('stores uploaded attachments on local disk even when default disk differs', function () {
    Queue::fake();
    Storage::fake('local');
    Storage::fake('public');
    config(['filesystems.default' => 'public']);

    $user = User::factory()->create();
    $attachment = UploadedFile::fake()->create('announcement.pdf', 20, 'application/pdf');

    $response = $this->actingAs($user)->post(route('bulk-email.send'), [
        'subject' => 'Important update',
        'message' => 'Please find the attachment.',
        'attachments' => [$attachment],
    ]);

    $response->assertRedirect();
    Queue::assertPushed(SendBulkEmailJob::class, function (SendBulkEmailJob $job) {
        $storedPath = $job->attachmentPaths[0] ?? null;

        return $storedPath !== null
            && Storage::disk('local')->exists($storedPath)
            && ! Storage::disk('public')->exists($storedPath);
    });
});

it('deletes temporary attachments even when mail sending throws an exception', function () {
    Storage::fake('local');
    $attachmentPath = 'temp-attachments/announcement.pdf';
    Storage::disk('local')->put($attachmentPath, 'dummy-pdf-content');
    User::factory()->create();

    Mail::partialMock()
        ->shouldReceive('to->send')
        ->andThrow(new \Exception('mail transport failed'));

    expect(fn () => (new SendBulkEmailJob(
        subject: 'Important update',
        message: 'Please find the attachment.',
        attachmentPaths: [$attachmentPath]
    ))->handle())->toThrow(\Exception::class);

    expect(Storage::disk('local')->exists($attachmentPath))->toBeFalse();
});
