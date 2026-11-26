<?php

namespace App\Members;

use App\Enums\Role;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The member list as a spreadsheet file (R1-15). Approved members only, a few columns,
 * no e-mail addresses. Cells that a spreadsheet would read as a formula are made harmless.
 */
final class MemberListCsv
{
    public const COLUMNS = ['First name', 'Last name', 'Status', 'Institution', 'Country', 'Member since', 'Roles', 'End of membership requested'];

    public function count(): int
    {
        return User::whereNotNull('approved_at')->count();
    }

    public function download(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::COLUMNS, ',', '"', '');

            User::whereNotNull('approved_at')->with('roles')->orderBy('last_name')->orderBy('first_name')->orderBy('id')
                ->chunk(200, function ($members) use ($out) {
                    foreach ($members as $member) {
                        fputcsv($out, array_map([self::class, 'safe'], [
                            $member->first_name,
                            $member->last_name,
                            $member->career_stage,
                            $member->institution,
                            $member->country,
                            $member->approved_at?->toDateString(),
                            $member->roles->sortBy(fn ($r) => array_search($r->role, Role::cases(), true))->map(fn ($r) => $r->role->label())->implode(', '),
                            $member->membership_end_requested_at?->toDateString(),
                        ]), ',', '"', '');
                    }
                });

            fclose($out);
        }, 'ern-members-'.now()->toDateString().'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /** A leading =, +, -, @, tab or carriage return would start a formula in Excel: add an apostrophe. */
    public static function safe(?string $value): string
    {
        $value = (string) $value;
        if ($value === '' || in_array($value[0], ["\t", "\r"], true)) {
            return $value === '' ? '' : "'".$value;
        }
        // Spreadsheet programs skip leading spaces and also read the full-width signs as formulas.
        // A value that is not valid UTF-8 keeps its raw start, so it is still checked.
        $first = mb_substr(preg_replace('/^[\s\x{00A0}\x{3000}]+/u', '', $value) ?? $value, 0, 1);

        return in_array($first, ['=', '+', '-', '@', '＝', '＋', '－', '＠'], true) ? "'".$value : $value;
    }
}
