<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One decision on an application (R1-10): who decided, what, when. It holds no name,
 * no e-mail address and no other detail of the applicant. For an approval, it keeps the
 * time the member agreed to the Code of Conduct and the privacy policy. email_sent_at says
 * whether the e-mail to the applicant went out.
 */
class MembershipDecision extends Model
{
    public const APPROVED = 'approved';

    public const DECLINED = 'declined';

    protected $guarded = ['*'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['applied_at' => 'date', 'agreed_at' => 'datetime', 'email_sent_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
