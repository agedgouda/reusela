<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaveSectionAction
{
    public function execute(Model $model, array $input, ?Model $parent = null, string $foreignKey = 'jurisdiction_id'): Model
    {
        $validator = Validator::make($input, [
            'section_title_id' => 'required|exists:section_titles,id',
            'text'             => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        if (!$model->exists && $parent) {
            $data[$foreignKey] = $parent->id;
        }

        $model->fill($data)->save();
        $model->load('sectionTitle');

        return $model;
    }
}
