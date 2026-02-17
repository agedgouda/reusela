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

<x-jurisdiction-card
    :editable="$editable"
    :variant="$title->title === 'Report a Violation' ? 'violation' : 'default'"
    wire:key="card-{{ $model->id }}-{{ $isEditing ? 'edit' : 'view' }}"
    class="w-full !items-stretch"
>
    {{-- Header Section: Flex wrapper with justify-between pushes items to opposite ends --}}
    <div class="w-full mb-[36px] flex items-center justify-between">

        {{-- Left Section: Icon and Title --}}
        <div class="flex flex-row gap-[18px] items-center h-[36px]">
            @if ($title->icon)
                <img class="h-8 w-auto" src="{{ Storage::url('icons/' . $title->icon) }}" alt="icon">
            @endif
            <span class="text-[28px] leading-[36px] tracking-[-0.05em] font-bold">
                {{ $title->title }}
            </span>
        </div>

        {{-- Right Section: Buttons --}}
        @if($editable && !$isEditing)
            <div class="flex items-center gap-2 shrink-0">
                <flux:button
                    wire:click="toggleEdit({{ $model->id }})"
                    variant="primary"
                    size="xs"
                    class="!bg-[#9adbe8] !text-[#15121b] hover:!bg-[#89c6d3]"
                >
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

    {{-- Content Section --}}
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

    {{-- Delete Modal --}}
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
