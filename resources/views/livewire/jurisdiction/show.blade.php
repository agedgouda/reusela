<div class="p-6 space-y-4">

        @if($editable)
        <a href="/jurisdictions" class="hover:text-blue-300 text-blue-800 mb-5"><- Back</a>
        @endif
        <div class="flex items-center">

            <h1 class="text-2xl font-bold">
                You're in
                @if($jurisdiction->name !== 'Unincorporated')
                the City of {{ $jurisdiction->name }}!
                @else
                {{ $jurisdiction->name }} LA County!
                @endif

            </h1>

            {{-- Add Section Button --}}
            @if($editable && !$showAddSectionForm)
            <flux:button color="blue" variant="primary" wire:click="$toggle('showAddSectionForm')" class="ml-auto">
                Add Section
            </flux:button>
            @endif
        </div>
    {{-- Add Section Form --}}
    @if($showAddSectionForm)
        <livewire:jurisdiction.edit-section :jurisdiction-id="$jurisdictionId" wire:key="edit-section-form" />
    @endif

    <div @class([
                'rich-text prose max-w-none dark:prose-invert transition-all',
                'border border-zinc-200 shadow-sm rounded-lg pt-3 px-3 pb-6 min-h-[20px]' => $editable,
            ])>
         @if($editable)
        <div class="flex items-center justify-between w-full">
            <div class="font-bold text-lg">
                Jurisdiction Information
            </div>

            <div class="flex items-center gap-4">
                @if (!$showGeneralInfoEdit)
                    <button wire:click="toggleEdit('general')" class="text-blue-600 hover:underline text-sm">
                        Edit
                    </button>
                @else
                    <button wire:click="toggleEdit('general')" class="text-blue-600 hover:underline text-sm">
                        Cancel
                    </button>
                @endif
            </div>
        </div>
        @endif
        @if(!$showGeneralInfoEdit)
            <div>
                @if($jurisdiction->general_information)
                    {!! $jurisdiction->general_information !!}
                @elseif(!$jurisdiction->general_information && $editable)
                     <div class="text-gray-400 text-center">- No Jurisdiction Information Entered Yet -</div>
                @endif
            </div>
        @else
            <livewire:jurisdiction.edit :jurisdiction="$jurisdiction"/>
        @endif
    </div>

    @if(!$jurisdiction->sections->isEmpty())
        <div class="space-y-3 mt-4">
            @foreach($jurisdiction->sections->sortBy(fn($s) => $s->sectionTitle->sort_order) as $section)
                <div @class([
                'rich-text prose max-w-none dark:prose-invert transition-all',
                'border border-zinc-200 shadow-sm rounded-lg pt-3 px-3 pb-6 min-h-[20px]' => $editable,
            ])>
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

                    <div class="mt-2 text-black dark:text-gray-200 rich-text">
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
