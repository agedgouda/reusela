<div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">City of {{ $jurisdiction->name }}</h1>

    {{-- Add Section Button --}}
    @if($editable && !$showAddSectionForm)
        <button wire:click="$toggle('showAddSectionForm')" class="bg-blue-600 text-white px-3 py-1 rounded">
            Add Section
        </button>
    @endif

    {{-- Add Section Form --}}
    @if($showAddSectionForm)
        <livewire:jurisdiction.edit-section :jurisdiction-id="$jurisdictionId" wire:key="edit-section-form" />
    @endif

    {{-- No Sections --}}
    @if($jurisdiction->sections->isEmpty())
        <p class="text-gray-500 mt-2">No sections yet.</p>
    @else
        <div class="space-y-3 mt-4">
            @foreach($jurisdiction->sections->sortBy(fn($s) => $s->sectionTitle->sort_order) as $section)
                <div class="p-4 border rounded shadow-sm bg-white dark:bg-zinc-700">
                    <div class="flex justify-between items-center">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">
                            <div class="flex items-center font-medium space-x-2">
                                @if ($section->sectionTitle->icon) <img class="h-5" src="/icons/{{ $section->sectionTitle->icon  }}" alt=""> @endif
                                <span>{{ $section->sectionTitle->title }}</span>
                            </div>
                        </div>
                        @if($editable)
                            {{-- Edit Button --}}
                            @if($editingSectionId != $section->id )
                            <button wire:click="toggleEditSection({{ $section->id }})"
                                    class="text-blue-600 hover:underline text-sm">
                                Edit
                            </button>
                            @else
                            <button wire:click="toggleEditSection({{ $section->id }})"
                                    class="text-blue-600 hover:underline text-sm">
                                Cancel
                            </button>
                            @endif
                        @endif
                    </div>

                    <div class="mt-2 text-gray-700 dark:text-gray-200">
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
</div>
