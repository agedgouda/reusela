<div class="mt-4 p-4 border rounded bg-white dark:bg-zinc-700 space-y-3 shadow-sm">

    {{-- Section title dropdown only when adding a new section --}}
    @if(!$section->exists)
        <div>
            <label class="block font-semibold mb-1">Section Title</label>

            <select wire:model="newSectionTitleId" class="border rounded px-2 py-1 w-full">
                <option value="">-- Select Section Title --</option>
                @foreach($availableSectionTitles as $title)
                    <option value="{{ $title->id }}">{{ $title->title }}</option>
                @endforeach
            </select>

            @error('newSectionTitleId')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    @endif

    {{-- TRIX Editor --}}
    <div wire:ignore.self>
        <label class="block font-semibold mb-1">Text</label>

        @php
            $inputId = 'section-text-' . ($section->exists ? $section->id : 'new');
        @endphp

        <input
            id="{{ $inputId }}"
            type="hidden"
            wire:model="newSectionText"
            value="{{ $newSectionText }}"
        >

        <trix-editor
            input="{{ $inputId }}"
            class="border rounded w-full bg-white dark:bg-zinc-700"
            style="min-height: 200px;"
        ></trix-editor>

        @error('newSectionText')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Save / Cancel Buttons --}}
    <div class="flex justify-end space-x-2 mt-2">

        <button wire:click="save(false)" class="bg-blue-600 text-white px-3 py-1 rounded">
            Save
        </button>

        @unless($section->exists)
            <button wire:click="save(true)" class="bg-green-600 text-white px-3 py-1 rounded">
                Save & Add New
            </button>

            <button wire:click="$dispatch('cancelAddSection')"
                    class="bg-gray-500 text-white px-3 py-1 rounded">
                Cancel
            </button>
        @else
            <button
                wire:click="$dispatch('toggleEditSection', { sectionId: {{ $section->id }} })"
                class="bg-gray-500 text-white px-3 py-1 rounded"
            >
                Cancel
            </button>
        @endunless
    </div>

</div>
