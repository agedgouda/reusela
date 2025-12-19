<div class="p-6 space-y-4">
    {{-- 1. Navigation --}}
    @if($editable)
        <a href="/jurisdictions" class="hover:text-blue-300 text-blue-800 mb-5 inline-block"><- Back</a>
    @endif

    {{-- 2. Header --}}
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            You're in {{ $jurisdiction->name !== 'Unincorporated' ? "the City of $jurisdiction->name!" : "$jurisdiction->name LA County!" }}
        </h1>

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
    {{-- 4. Jurisdiction General Information --}}
    <x-jurisdiction-card :editable="$editable">
        @if($editable)
            <div class="jurisdiction-card-header">
                <div class="font-bold text-lg text-black dark:text-white">Jurisdiction Information</div>
                @if(!$showGeneralInfoEdit )
                <flux:button wire:click="toggleEdit('general')" color="blue" variant="primary" size="xs">
                   Edit
                </flux:button>
                @endif
            </div>
        @endif

        <div class="mt-2">
            @if(!$showGeneralInfoEdit)
                @if($jurisdiction->general_information)
                    {!! $jurisdiction->general_information !!}
                @elseif($editable)
                    <div class="text-gray-400 text-center py-4">- No Jurisdiction Information Entered Yet -</div>
                @endif
            @else
                <livewire:jurisdiction.edit :jurisdiction="$jurisdiction" wire:key="general-edit-form" />
            @endif
        </div>
    </x-jurisdiction-card>

    {{-- 5. Dynamic Sections Loop --}}
    @if($jurisdiction->sections->isNotEmpty())
        <div class="space-y-3 mt-4">
        @foreach($jurisdiction->sections->sortBy(fn($s) => $s->sectionTitle->sort_order) as $section)
            <x-section-display
                :model="$section"
                :editable="$editable"
                :editing-id="$editingSectionId"
                :parent="$jurisdiction"
                wire:key="section-wrapper-{{ $section->id }}-{{ $editingSectionId }}"
            />
        @endforeach
        </div>
    @else
        <livewire:jurisdiction.default-content/>
    @endif

    {{-- 6. Shared Content --}}
    <livewire:jurisdiction.shared-content />
</div>
