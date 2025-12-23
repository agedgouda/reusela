@props([
    'model',
    'editable' => false,
    'editingId' => null,
    'parent' => null,
    'foreignKey' => 'jurisdiction_id'
])

@php
    $isEditing = $editingId == $model->id;
    $title = $model->sectionTitle;
@endphp

<x-jurisdiction-card :editable="$editable" wire:key="card-{{ $model->id }}-{{ $isEditing ? 'edit' : 'view' }}">
    <div class="jurisdiction-card-header">
        <div class="flex items-center font-semibold text-gray-800 dark:text-gray-100 space-x-2">
            @if ($title->icon)
                <img class="h-8" src="/icons/{{ Storage::url('icons/' . $title->icon) }}" alt="icon">
            @endif
            <span class="text-xl m-0">{{ $title->title }}</span>
        </div>

        @if($editable && !$isEditing)
            <div class="flex items-center space-x-2">
                <flux:button wire:click="toggleEdit({{ $model->id }})" color="blue" variant="primary" size="xs">
                    Edit
                </flux:button>

                <flux:modal.trigger name="delete-{{ $model->id }}">
                    <flux:button variant="danger" size="xs">
                        Delete
                    </flux:button>
                </flux:modal.trigger>
            </div>
        @endif
    </div>

    <div class="mt-2 text-black dark:text-gray-200">
        @if(!$isEditing)
            {!! $model->text !!}
        @else
            <livewire:section.edit-section
                :model="$model"
                :parent-model="$parent"
                :foreign-key="$foreignKey"
                wire:key="edit-inst-{{ $model->id }}"
            />
        @endif
    </div>

    <flux:modal name="delete-{{ $model->id }}" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Section?</flux:heading>
                <flux:subheading>Are you sure you want to delete "{{ $title->title }}"? This action cannot be undone.</flux:subheading>
            </div>

            <div class="flex space-x-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteSection({{ $model->id }})" variant="danger">
                    Confirm Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-jurisdiction-card>
