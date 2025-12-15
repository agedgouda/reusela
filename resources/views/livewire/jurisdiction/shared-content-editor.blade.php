<div>
    {{-- TRIX Editor --}}
    <div wire:ignore>
        <label class="block font-semibold mb-1">Text</label>

        {{-- Simple wire:model binding, Trix loads the initial content naturally --}}
        <input
            id="jurisdiction-content"
            type="hidden"
            wire:model="content"
            value="{{ $content }}"
        >

        <trix-editor
            input="jurisdiction-content"
            class="border rounded w-full bg-white dark:bg-zinc-700"
            style="min-height: 200px;"
        ></trix-editor>

        @error('content')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-end space-x-2 mt-2">
        <flux:button wire:click="save" variant="primary" class="bg-blue-600">
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
