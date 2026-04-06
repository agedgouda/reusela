<?php

namespace App\Console\Commands;

use App\Models\Jurisdiction;
use App\Models\Section;
use App\Support\HtmlLinks;
use Illuminate\Console\Command;

class NormalizeSectionLinks extends Command
{
    protected $signature = 'sections:normalize-links';

    protected $description = 'Ensure all anchor tags in sections and jurisdiction general information open in a new tab (target="_blank")';

    public function handle(): int
    {
        $this->normalizeSections();
        $this->normalizeJurisdictions();

        return Command::SUCCESS;
    }

    private function normalizeSections(): void
    {
        $sections = Section::query()
            ->whereNotNull('text')
            ->where('text', 'like', '%<a%')
            ->get();

        $this->info("Found {$sections->count()} section(s) containing links.");

        $updated = 0;

        foreach ($sections as $section) {
            $normalized = HtmlLinks::ensureNewTab($section->text);

            if ($normalized !== $section->text) {
                $section->update(['text' => $normalized]);
                $updated++;
            }
        }

        $this->info("Updated {$updated} section(s).");
    }

    private function normalizeJurisdictions(): void
    {
        $jurisdictions = Jurisdiction::withoutGlobalScope('excludeDefault')
            ->whereNotNull('general_information')
            ->where('general_information', 'like', '%<a%')
            ->get();

        $this->info("Found {$jurisdictions->count()} jurisdiction(s) with links in general information.");

        $updated = 0;

        foreach ($jurisdictions as $jurisdiction) {
            $normalized = HtmlLinks::ensureNewTab($jurisdiction->general_information);

            if ($normalized !== $jurisdiction->general_information) {
                $jurisdiction->update(['general_information' => $normalized]);
                $updated++;
            }
        }

        $this->info("Updated {$updated} jurisdiction(s).");
    }
}
