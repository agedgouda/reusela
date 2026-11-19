<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesIconUpload
{
    /**
     * Validation rules for an uploaded icon.
     *
     * @return array<int, string>
     */
    protected function iconRules(): array
    {
        return ['image', 'mimes:png,jpg,jpeg,webp', 'max:2048'];
    }

    /**
     * Store a new icon under a server-generated name, deleting the old one if replaced.
     *
     * The stored name is a random hash with an extension derived from the file's
     * contents, never the client-supplied filename, so an upload cannot be saved
     * with an executable extension or overwrite another file.
     *
     * @return string|null The stored filename (relative to the icons directory) or null
     */
    public function saveIcon(?UploadedFile $newIcon, ?string $oldIcon = null): ?string
    {
        if (! $newIcon) {
            return $oldIcon;
        }

        $storedPath = $newIcon->store('icons', 'public');

        if ($storedPath === false) {
            throw new \RuntimeException('The icon could not be stored.');
        }

        if ($oldIcon) {
            Storage::disk('public')->delete('icons/'.basename($oldIcon));
        }

        return basename($storedPath);
    }
}
