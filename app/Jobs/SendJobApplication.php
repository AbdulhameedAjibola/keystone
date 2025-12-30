<?php

namespace App\Jobs;

use App\Mail\SendJobApplicationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;

class SendJobApplication implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $phoneNumber,
        public string $jobTitle,
        public string $message,
        public string $resume
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to('akoredeajibola091@gmail.com')
            ->send(
                new SendJobApplicationMail(
                $this->name,
                $this->email,
                $this->phoneNumber,
                $this->jobTitle, 
                $this->message, 
                $this->resume
            )
        );
    }
}
