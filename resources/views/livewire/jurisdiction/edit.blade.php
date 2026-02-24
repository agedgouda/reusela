<div class="w-full"
     x-data="setupTinyMCE('{{ $this->getId() }}', @entangle('text'))"
     x-init="initTiny()">

    <div wire:ignore class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-sm">
        {{-- Toolbar area --}}
        <div id="tiny-toolbar-{{ $this->getId() }}" class="bg-zinc-50 border-b border-zinc-200 min-h-[40px]"></div>

        {{-- Editor area --}}
        <div
            x-ref="tinyditor"
            class="rich-text prose max-w-none p-4 min-h-[300px] focus:outline-none"
        >
            {!! $text !!}
        </div>
    </div>

    <div class="flex justify-end space-x-2 mt-4">
        <flux:button wire:click="save" color="blue" variant="primary">Save Changes</flux:button>
        <flux:button wire:click="cancel" class="bg-zinc-200 text-zinc-700 px-4 py-2 rounded-lg text-sm">Cancel</flux:button>
    </div>
</div>
