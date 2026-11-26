<?php

namespace App\Console\Commands;

use App\Models\SystemValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Stores the time of the last scheduled run, so the System page can show that the
 * hosting's cron entry works.
 */
#[Signature('ern:heartbeat')]
#[Description('Store the time of the last scheduled run')]
class Heartbeat extends Command
{
    public function handle(): int
    {
        SystemValue::put(SystemValue::LAST_SCHEDULED_RUN, now()->utc()->toIso8601String());

        return self::SUCCESS;
    }
}
