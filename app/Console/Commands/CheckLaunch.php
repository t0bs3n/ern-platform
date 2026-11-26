<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Says whether the platform may go live. The live upload (R0-10) runs it first and stops if it fails.
 * Today it checks one thing: the privacy policy is the board's text, not the placeholder
 * (neither its sentence nor its link to the old site's policy may remain).
 */
#[Signature('ern:check-launch')]
#[Description('Refuse to go live while the privacy policy is still the placeholder')]
class CheckLaunch extends Command
{
    public function handle(): int
    {
        $privacy = Page::where('slug', 'privacy')->value('body');

        if ($privacy === null || trim($privacy) === '') {
            $this->error('Not ready to go live: the privacy policy page is missing.');

            return self::FAILURE;
        }

        if (str_contains($privacy, Page::PRIVACY_PLACEHOLDER) || str_contains($privacy, 'esportsresearch.net/imprint')) {
            $this->error('Not ready to go live: the privacy policy is still the placeholder. The board has to deliver the text (docs/HUMAN_TODO.md, R1-05).');

            return self::FAILURE;
        }

        $this->info('Ready to go live: the privacy policy is in place.');

        return self::SUCCESS;
    }
}
