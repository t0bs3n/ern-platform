<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Small values the platform keeps about itself, such as the time of the last scheduled run.
 */
#[Fillable(['key', 'value'])]
class SystemValue extends Model
{
    public const LAST_SCHEDULED_RUN = 'last_scheduled_run';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public static function put(string $key, string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function time(string $key): ?CarbonImmutable
    {
        $value = static::query()->find($key)?->value;

        return $value === null ? null : CarbonImmutable::parse($value);
    }
}
