<div
    x-data
    x-init="
        const input = $refs.input;
        const editor = $refs.editor;

        editor.editor.loadHTML(input.value);

        editor.addEventListener('trix-change', () => {
            $wire.set('text', editor.editor.getDocument().toString());
        });
    "
>
    <div wire:ignore>
        <input
            x-ref="input"
            type="hidden"
            value="{{ $text }}"
        >

        <trix-editor
            x-ref="editor"
            class="w-full bg-white dark:bg-zinc-700"
            style="min-height: 200px;"
        ></trix-editor>
    </div>

    <div class="flex justify-end space-x-2 mt-2">
        <button
            wire:click="save"
            class="bg-blue-600 text-white px-3 py-1 rounded"
        >
            Save
        </button>

        <button
            wire:click="cancel"
            class="bg-gray-500 text-white px-3 py-1 rounded"
        >
            Cancel
        </button>
    </div>
</div>
