<div class="rich-text"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('content'))"
     x-init="initTiny()">

    {{-- TinyMCE Inline Editor --}}
    <div class="space-y-1">
        <label class="block font-semibold mb-1 text-black dark:text-white">Text</label>

        <div wire:ignore class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
            {{-- Toolbar area anchored to this specific component ID --}}
            <div id="tiny-toolbar-{{ $this->getId() }}" class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 min-h-[40px]"></div>

            {{-- Editor area --}}
            <div
                x-ref="tinyditor"
                class="p-4 min-h-[300px] focus:outline-none text-black dark:text-white prose dark:prose-invert max-w-none"
            >
                {!! $content !!}
            </div>
        </div>

        @error('content')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-end space-x-2 mt-2">
        <flux:button wire:click="save" color="blue" variant="primary">
            Save
        </flux:button>

        {{-- This button now triggers the redirect --}}
        <flux:button wire:click="cancel">
            Cancel
        </flux:button>
    </div>

    @if(session()->has('saved'))
        <p class="text-green-600 text-sm mt-2">Saved successfully.</p>
    @endif
</div>
