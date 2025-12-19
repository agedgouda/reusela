<div class="mt-4">
    {{-- Header Row --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Default City Information</h2>

        @if(!$showAddSectionForm)
            <flux:button wire:click="$set('showAddSectionForm', true)" variant="primary"  color="blue">
                Add Section
            </flux:button>
        @endif
    </div>

    <div class="space-y-4">
        {{-- Add Form --}}
        @if($showAddSectionForm)
            <livewire:section.edit-section
                :model="new \App\Models\DefaultSection"
                :parent-model="null"
                wire:key="add-default-section"
            />
        @endif

        {{-- Sections List --}}
        @foreach($defaultSections as $default)
            @php $isEditing = $editingSectionId == $default->id; @endphp

            <x-jurisdiction-card :editable="true" wire:key="default-card-{{ $default->id }}-{{ $isEditing ? 'edit' : 'view' }}">
                <div class="jurisdiction-card-header">
                    <div class="flex items-center font-semibold text-gray-800 dark:text-gray-100 space-x-2">
                        @if ($default->sectionTitle->icon)
                            <img class="h-8" src="/icons/{{ $default->sectionTitle->icon }}" alt="icon">
                        @endif
                        <span class="text-xl m-0">{{ $default->sectionTitle->title }}</span>
                    </div>

                    @if(!$isEditing)
                        <div class="flex items-center space-x-2">
                            <flux:button wire:click="toggleEdit({{ $default->id }})" color="blue" variant="primary" size="xs">
                                Edit
                            </flux:button>

                            <flux:modal.trigger name="delete-default-{{ $default->id }}">
                                <flux:button variant="danger" size="xs">Delete</flux:button>
                            </flux:modal.trigger>
                        </div>
                    @endif
                </div>

                <div class="mt-2 text-black dark:text-gray-200">
                    @if(!$isEditing)
                        {!! $default->text !!}
                    @else
                        <livewire:section.edit-section
                            :model="$default"
                            :parent-model="null"
                            wire:key="edit-default-{{ $default->id }}"
                        />
                    @endif
                </div>

                {{-- Delete Modal --}}
                <flux:modal name="delete-default-{{ $default->id }}" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Delete Default Section?</flux:heading>
                            <flux:subheading>Are you sure? This will remove this default content for all future jurisdictions.</flux:subheading>
                        </div>

                        <div class="flex space-x-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button wire:click="deleteSection({{ $default->id }})" variant="danger">
                                Confirm Delete
                            </flux:button>
                        </div>
                    </div>
                </flux:modal>
            </x-jurisdiction-card>
        @endforeach
    </div>
</div>
