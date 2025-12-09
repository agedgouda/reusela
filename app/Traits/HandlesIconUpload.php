<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HandlesIconUpload
{
    /**
     * Store a new icon file, optionally deleting an old one.
     *
     * @param \Livewire\TemporaryUploadedFile|null $newIcon
     * @param string|null $oldIcon
     * @return string|null The stored filename or null
     */
    public function saveIcon($newIcon, $oldIcon = null)
    {
        if (!$newIcon) {
            return $oldIcon; // Nothing new uploaded, keep old
        }

        $originalName = $newIcon->getClientOriginalName();
        $safeName = $originalName;

        // Avoid collisions
        if (file_exists(public_path("icons/$safeName"))) {
            $safeName = Str::uuid() . '_' . $originalName;
        }

        $newIcon->storeAs('icons', $safeName, 'public');

        // Delete old icon if it exists
        if ($oldIcon && file_exists(public_path("icons/$oldIcon"))) {
            @unlink(public_path("icons/$oldIcon"));
        }

        return $safeName;
    }
}
