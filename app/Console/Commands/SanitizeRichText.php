<?php

namespace App\Console\Commands;

use App\Models\Jurisdiction;
use App\Models\Section;
use App\Models\SharedContent;
use App\Support\RichTextSanitizer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SanitizeRichText extends Command
{
    protected $signature = 'content:sanitize {--dry-run : List records that would change without saving them}';

    protected $description = 'Sanitize stored rich-text HTML (sections, jurisdiction general information, shared content)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $changed = $this->sanitize(Section::query(), 'text', 'sections', $dryRun)
            + $this->sanitize(Jurisdiction::withoutGlobalScope('excludeDefault'), 'general_information', 'jurisdictions', $dryRun)
            + $this->sanitize(SharedContent::query(), 'content', 'shared content', $dryRun);

        if (! $dryRun && $changed > 0) {
            cache()->forget('shared.jurisdiction.show');
        }

        $this->info($dryRun
            ? "Dry run: {$changed} record(s) would change. Run without --dry-run to save."
            : "Sanitized {$changed} record(s).");

        return self::SUCCESS;
    }

    /**
     * @param  Builder<covariant Model>  $query
     */
    private function sanitize(Builder $query, string $column, string $label, bool $dryRun): int
    {
        $changed = 0;

        foreach ($query->whereNotNull($column)->lazyById(100) as $record) {
            $rawValue = $record->getRawOriginal($column);
            $original = is_scalar($rawValue) ? (string) $rawValue : null;
            $recordKey = $record->getKey();
            $clean = RichTextSanitizer::clean($original);

            if ($clean === $original) {
                continue;
            }

            $changed++;

            if ($dryRun) {
                $this->line("<comment>{$label} #".(is_scalar($recordKey) ? $recordKey : '?').'</comment>');
                $this->line('  before: '.$this->excerpt($original));
                $this->line('  after:  '.$this->excerpt($clean));

                continue;
            }

            $record->forceFill([$column => $clean])->save();
        }

        $this->line("{$label}: {$changed} changed");

        return $changed;
    }

    private function excerpt(?string $html): string
    {
        return str((string) $html)->squish()->limit(200)->toString();
    }
}
