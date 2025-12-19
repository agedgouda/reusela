<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;

class DeleteSectionAction
{
    public function execute(Model $section): string
    {
        // Get the name before the record is destroyed
        $section->loadMissing('sectionTitle');
        $name = $section->sectionTitle->title ?? 'Section';

        $section->delete();

        return $name;
    }
}
