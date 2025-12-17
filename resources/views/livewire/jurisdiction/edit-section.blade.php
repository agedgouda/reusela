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
        <flux:button wire:click="save(false)" variant="primary" color="blue">
            Save
        </flux:button>
        @unless($section->exists)
            <flux:button wire:click="save(true)" variant="primary" color="green">
                Save & Add New
            </flux:button>

            <flux:button wire:click="$dispatch('cancelAddSection')">
                Cancel
            </flux:button>
        @else
            <flux:button wire:click="cancel">
                Cancel
            </flux:button>
        @endunless

    </div>
</div>
