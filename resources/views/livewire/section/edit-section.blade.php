<div class="mt-4 p-4 border rounded bg-white dark:bg-zinc-700 space-y-3 shadow-sm rich-text"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('newSectionText'))"
     x-init="initTiny()"
     x-on:section-saved.window="clearEditor()">

    {{-- Section title dropdown: Only show when creating a new record --}}
    @if(!$model->exists)
    <div>
        <label class="block font-semibold mb-1 text-black dark:text-white">Section Title</label>
        <select wire:model="newSectionTitleId" class="border rounded px-2 py-1 w-full bg-white dark:bg-zinc-800 text-black dark:text-white border-zinc-300 dark:border-zinc-600">
            <option value="">-- Select Section Title --</option>
            @foreach($availableSectionTitles as $title)
                <option value="{{ $title->id }}" wire:key="title-{{ $title->id }}">
                    {{ $title->title }}
                </option>
            @endforeach
        </select>
        @error('newSectionTitleId')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
    @endif

    {{-- TinyMCE Inline Editor --}}
    <div class="space-y-1">
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

        @unless($model->exists)
            {{-- Logic to hide "Save & Add New" if it's the last available title --}}
            @if(count($availableSectionTitles) > 1)
                <flux:button wire:click="save(true)" variant="primary" color="green">
                    Save & Add New
                </flux:button>
            @endif

            <flux:button wire:click="$dispatch('cancelAddSection')">
                Cancel
            </flux:button>
        @else
            {{-- Editing an existing record --}}
            <flux:button wire:click="closeEditor">
                Cancel
            </flux:button>
        @endunless
    </div>
</div>
