<div class="w-full"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('text'))"
     x-init="initTiny()">

    <div wire:ignore class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        {{-- Toolbar area --}}
        <div id="tiny-toolbar-{{ $this->getId() }}" class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 min-h-[40px]"></div>

        {{-- Editor area --}}
        <div
            x-ref="tinyditor"
            class="rich-text prose max-w-none p-4 min-h-[300px] focus:outline-none dark:prose-invert"
        >
            {!! $text !!}
        </div>
    </div>

    <div class="flex justify-end space-x-2 mt-4">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Save Changes</button>
        <button wire:click="cancel" class="bg-zinc-200 text-zinc-700 px-4 py-2 rounded-lg text-sm">Cancel</button>
    </div>
</div>
