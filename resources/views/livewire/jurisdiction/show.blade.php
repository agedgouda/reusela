<div class="p-6 space-y-4">
    {{-- 1. Navigation --}}
    @if($editable)
        <a href="/jurisdictions" class="hover:text-blue-300 text-blue-800 mb-5 inline-block"><- Back</a>
    @endif

    {{-- 2. Header --}}
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
        @if($jurisdiction->is_system_default)
            Default Information
        @else
            You're in {{ $jurisdiction->name !== 'Unincorporated' ? "the City of $jurisdiction->name!" : "$jurisdiction->name LA County!" }}
        @endif
        </h1>

        {{-- Add Section button only shows if the user is allowed to edit THIS record --}}
        @if($editable && !$showAddSectionForm && $showAddSectionButton)
            <flux:button color="blue" variant="primary" wire:click="$toggle('showAddSectionForm')" class="ml-auto">
                Add Section
            </flux:button>
        @endif
    </div>

    {{-- 3. Add Section Form --}}
    @if($showAddSectionForm)
        <div class="mb-4">
            <livewire:section.edit-section
                :model="new \App\Models\Section"
                :parent-model="$jurisdiction"
                foreign-key="jurisdiction_id"
                wire:key="add-section-form"
            />
        </div>
    @endif

    {{-- 4. Jurisdiction General Information Card --}}
    <x-jurisdiction-card :editable="$editable">
        @php
            // Logic: Only show the "Edit" header/button if local info exists.
            // If general_information is null/empty, we are viewing the default and shouldn't edit.
            $hasLocalInfo = !empty($jurisdiction->general_information);
            $canEditInfo = $editable && ($hasLocalInfo || $jurisdiction->is_system_default);
        @endphp

        @if($canEditInfo)
            <div class="jurisdiction-card-header flex justify-between items-center">
                <div class="font-bold text-lg text-black dark:text-white">Jurisdiction Information</div>
                @if(!$showGeneralInfoEdit)
                    <flux:button wire:click="toggleEdit('general')" color="blue" variant="primary" size="xs">
                        Edit
                    </flux:button>
                @endif
            </div>
        @endif

        <div class="mt-2">
            @if(!$showGeneralInfoEdit)
                <div class="rich-text prose max-w-none dark:prose-invert">
                    {{-- Accessor handles local vs default waterfall --}}
                    {!! $jurisdiction->display_general_info !!}
                </div>
            @else
                {{-- This only triggers if $canEditInfo was true --}}
                <livewire:jurisdiction.edit :jurisdiction="$jurisdiction" wire:key="general-edit-form" />
            @endif
        </div>
    </x-jurisdiction-card>

    {{-- 5. Dynamic Sections Loop --}}
    <div class="space-y-3 mt-4">
        @foreach($jurisdiction->display_sections as $section)
            @php
                // Logic: A section is editable only if it belongs to the current jurisdiction.
                // If the loop is using Master sections, the ID won't match, so $canEdit is false.
                $isLocalSection = $section->jurisdiction_id === $jurisdiction->id;
                $canEditThisSection = $editable && ($isLocalSection || $jurisdiction->is_system_default);
            @endphp

            <x-section-display
                :model="$section"
                :editable="$canEditThisSection"
                :editing-id="$editingSectionId"
                :parent="$jurisdiction"
                wire:key="section-wrapper-{{ $section->id }}-{{ $editingSectionId }}"
            />
        @endforeach

        {{-- Fallback empty state if even the default has no sections --}}
        @if($jurisdiction->display_sections->isEmpty())
             <div class="text-gray-400 text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                No sections available.
            </div>
        @endif
    </div>

    {{-- 6. Shared Content (Statewide Laws) --}}
    <div class="mt-10 pt-6 border-t border-gray-100">
        <livewire:jurisdiction.shared-content contentKey="jurisdiction.show" />
    </div>
</div>
