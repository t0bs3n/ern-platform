<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Remove expired password reset links (they hold the e-mail address) every day.
Schedule::command('auth:clear-resets')->daily();

// Proof that the hosting's cron entry runs the scheduler (shown on the admin System page).
Schedule::command('ern:heartbeat')->everyMinute();
