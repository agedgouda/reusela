<div class="mt-4 p-4 border rounded bg-white dark:bg-zinc-700 space-y-3 shadow-sm rich-text"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('newSectionText'))"
     x-init="initTiny()">

    {{-- Section title dropdown only when adding a new section --}}
    @if(!$section->exists)
        <div wire:ignore>
            <label class="block font-semibold mb-1 text-black dark:text-white">Section Title</label>
            <select wire:model="newSectionTitleId" class="border rounded px-2 py-1 w-full bg-white dark:bg-zinc-800 text-black dark:text-white border-zinc-300 dark:border-zinc-600">
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

    {{-- TinyMCE Inline Editor --}}
    <div class="space-y-1">
        <label class="block font-semibold mb-1 text-black dark:text-white">Text</label>

        <div wire:ignore class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
            {{-- Toolbar area anchored to this specific component ID --}}
            <div id="tiny-toolbar-{{ $this->getId() }}" class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 min-h-[40px]"></div>

            {{-- Editor area --}}
            <div
                x-ref="tinyditor"
                class="p-4 min-h-[250px] focus:outline-none text-black dark:text-white prose dark:prose-invert max-w-none"
            >
                {!! $newSectionText !!}
            </div>
        </div>

        @error('newSectionText')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Save / Cancel Buttons --}}
    <div class="flex justify-end space-x-2 mt-4">
        <button wire:click="save(false)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Save
        </button>

        @unless($section->exists)
            <button wire:click="save(true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Save & Add New
            </button>

            <button wire:click="$dispatch('cancelAddSection')"
                    class="bg-zinc-500 hover:bg-zinc-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Cancel
            </button>
        @else
            <button
                wire:click="cancel"
                class="bg-zinc-500 hover:bg-zinc-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
            >
                Cancel
            </button>
        @endunless
    </div>
</div>
