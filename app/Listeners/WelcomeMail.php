<?php

namespace App\Listeners;

use App\Events\NewAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class WelcomeMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewAccount $event): void
    {
        Mail::to($event->user->email)->send(new \App\Mail\VerifyEmail($event->user, $event->verificationUrl));
    }
}
