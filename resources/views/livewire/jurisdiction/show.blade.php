<div class="p-6 space-y-4">

        @if($editable)
        <a href="/jurisdictions" class="hover:text-blue-300 text-blue-800 mb-5"><- Back</a>
        @endif
        <div class="flex items-center">

            <h1 class="text-2xl font-bold">
                @if($jurisdiction->name !== 'Unincorporated')
                City of {{ $jurisdiction->name }}
                @else
                {{ $jurisdiction->name }} LA County
                @endif
            </h1>

            {{-- Add Section Button --}}
            @if($editable && !$showAddSectionForm)
            <x-primary-button wire:click="$toggle('showAddSectionForm')" class="ml-auto">
                Add Section
            </x-primary-button>
            @endif
        </div>
    {{-- Add Section Form --}}
    @if($showAddSectionForm)
        <livewire:jurisdiction.edit-section :jurisdiction-id="$jurisdictionId" wire:key="edit-section-form" />
    @endif

    <div class="bg-white text-black dark:bg-zinc-700">
        <div class="flex justify-end">
            @if($editable)
                <button wire:click="toggleEdit('general')" class="text-blue-600 hover:underline text-sm">
                    @if (!$showGeneralInfoEdit )
                        Edit
                    @else
                        Cancel
                    @endif
                </button>
            @endif
        </div>
        @if(!$showGeneralInfoEdit)
        <div class="mt-2 text-black dark:text-gray-200">
            {!! $jurisdiction->general_information !!}
        </div>
        @else
            <livewire:jurisdiction.edit :jurisdiction="$jurisdiction"/>
        @endif
    </div>

    @if(!$jurisdiction->sections->isEmpty())
        <div class="space-y-3 mt-4">
            @foreach($jurisdiction->sections->sortBy(fn($s) => $s->sectionTitle->sort_order) as $section)
                <div class="bg-white text-black dark:bg-zinc-700">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="flex items-center font-semibold text-gray-800 dark:text-gray-100 space-x-2">
                                @if ($section->sectionTitle->icon) <img class="h-8" src="/icons/{{ $section->sectionTitle->icon }}" alt=""> @endif
                                <span class="text-xl">{{ $section->sectionTitle->title }}</span>
                            </div>
                        </div>

                        @if($editable)
                            @if($editingSectionId != $section->id)
                            <button wire:click="toggleEdit({{ $section->id }})"
                                    class="text-blue-600 hover:underline text-sm">
                                Edit
                            </button>
                            @else
                            <button wire:click="toggleEdit({{ $section->id }})"
                                    class="text-blue-600 hover:underline text-sm">
                                Cancel
                            </button>
                            @endif
                        @endif
                    </div>

                    <div class="mt-2 text-black dark:text-gray-200">
                        @if($editingSectionId === $section->id)
                            {{-- Livewire EditSection Component --}}
                            <livewire:jurisdiction.edit-section
                                :section="$section"
                                :jurisdiction-id="$jurisdictionId"
                                wire:key="edit-section-{{ $section->id }}" />
                        @else
                            {!! $section->text !!}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <livewire:jurisdiction.shared-content />
</div>
