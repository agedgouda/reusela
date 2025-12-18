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
            <livewire:jurisdiction.edit-section :jurisdiction-id="$jurisdictionId" wire:key="edit-section-form" />
        </div>
    @endif

    {{-- 4. Jurisdiction General Information --}}
    <x-jurisdiction-card :editable="$editable">
        @if($editable)
            <div class="jurisdiction-card-header">
                <div class="font-bold text-lg text-black dark:text-white">Jurisdiction Information</div>
                <button wire:click="toggleEdit('general')" class="text-blue-600 hover:underline text-sm">
                    {{ $showGeneralInfoEdit ? 'Cancel' : 'Edit' }}
                </button>
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
                <x-jurisdiction-card :editable="$editable" wire:key="section-card-{{ $section->id }}">

                    <div class="jurisdiction-card-header">
                        <div class="flex items-center font-semibold text-gray-800 dark:text-gray-100 space-x-2">
                            @if ($section->sectionTitle->icon)
                                <img class="h-8" src="/icons/{{ $section->sectionTitle->icon }}" alt="icon">
                            @endif
                            <span class="text-xl m-0">{{ $section->sectionTitle->title }}</span>
                        </div>

                        @if($editable)
                            <button wire:click="toggleEdit({{ $section->id }})" class="text-blue-600 hover:underline text-sm">
                                {{ $editingSectionId == $section->id ? 'Cancel' : 'Edit' }}
                            </button>
                        @endif
                    </div>

                    <div class="mt-2 text-black dark:text-gray-200">
                        @if($editingSectionId !== $section->id)
                            {!! $section->text !!}
                        @else
                            <livewire:jurisdiction.edit-section
                                :section="$section"
                                :jurisdiction-id="$jurisdictionId"
                                wire:key="edit-section-{{ $section->id }}" />
                        @endif
                    </div>
                </x-jurisdiction-card>
            @endforeach
        </div>
    @endif

    {{-- 6. Shared Content --}}
    <livewire:jurisdiction.shared-content />
</div>
