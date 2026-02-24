<div class="mt-4 p-4 border rounded bg-white space-y-3 shadow-sm rich-text"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('newSectionText'))"
     x-init="initTiny()"
     x-on:section-added.window="clearEditor()"> {{-- Fixed extra quote --}}

    {{-- Section title dropdown --}}
    @if(!$section->exists)
    <div>
        <label class="block font-semibold mb-1 text-black">Section Title</label>
        {{-- Use wire:model.live if you want immediate validation, otherwise wire:model is fine --}}
        <select wire:model="newSectionTitleId" class="border rounded px-2 py-1 w-full bg-white text-black">
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
        <label class="block font-semibold mb-1 text-black ">Text</label>

        <div wire:ignore class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-sm">
            {{-- Toolbar area anchored to this specific component ID --}}
            <div id="tiny-toolbar-{{ $this->getId() }}" class="bg-zinc-50 border-b border-zinc-200 min-h-[40px]"></div>

            {{-- Editor area --}}
            <div
                x-ref="tinyditor"
                class="p-4 min-h-[250px] focus:outline-none text-black  prose max-w-none"
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
            @if($availableSectionTitles->count() > 1)
            <flux:button wire:click="save(true)" variant="primary" color="green">
                Save & Add New
            </flux:button>
            @endif
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
