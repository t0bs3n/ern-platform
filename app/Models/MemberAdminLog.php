<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One line of the members log (R1-15): a role given or removed, or the list exported. */
class MemberAdminLog extends Model
{
    public const ROLE_GIVEN = 'role_given';

    public const ROLE_REMOVED = 'role_removed';

    public const LIST_EXPORTED = 'list_exported';

    public const UPDATED_AT = null;

    protected $table = 'member_admin_log';

    protected $guarded = ['*'];

    public static function write(string $action, ?int $actor, ?int $member = null, ?string $role = null, ?string $detail = null): void
    {
        (new static)->forceFill([
            'action' => $action, 'actor_id' => $actor, 'member_id' => $member, 'role' => $role, 'detail' => $detail,
        ])->save();
    }
}
