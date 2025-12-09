@props([
    'label' => 'Upload File',
    'currentFile' => null
])

<div
    x-data="fileUpload()"
    class="w-full"
>

    <div
         x-on:dragover.prevent="dragOver = true"
         x-on:dragleave.prevent="dragOver = false"
         x-on:drop.prevent="handleDrop($event)"
         :class="{'border-green-500': dragOver}"
         class="border-2 border-dashed rounded p-4 text-center cursor-pointer hover:border-green-500"
    >

        <label class="block font-semibold mb-2">{{ $label }}</label>

        {{-- Hidden input; wire:model is passed in from the parent --}}
        <input type="file" x-ref="fileInput" {{ $attributes }} class="hidden" @change="previewFile">

        <div class="text-gray-500 mb-2">
            Drag & drop here, or
            <span class="text-blue-600 underline cursor-pointer" @click.prevent="selectFile">browse</span>
        </div>

        {{-- Progress bar --}}
        <div wire:loading wire:target="{{ $attributes->wire('model') }}" class="w-full bg-gray-200 rounded h-2 mt-2">
            <div class="bg-green-500 h-2 rounded" style="width: 100%"></div>
        </div>

        {{-- Preview of new upload --}}
        <template x-if="preview">
            <div class="mt-3">
                <p class="text-sm text-gray-600 mb-1">Preview:</p>
                <img :src="preview" class="h-20 w-20 object-contain rounded border mx-auto">
                <button type="button" @click="removeFile" class="mt-2 text-red-600 underline text-sm">Remove</button>
            </div>
        </template>

        {{-- Optional current file for Edit page --}}
        @if($currentFile)
            <div x-show="!preview" class="mt-3">
                <p class="text-sm text-gray-600 mb-1">Current File:</p>
                <img src="/icons/{{ $currentFile }}" class="h-20 w-20 object-contain rounded border mx-auto">
            </div>
        @endif

        {{-- Validation error --}}
        @error($attributes->wire('model')) <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<script>
function fileUpload() {
    return {
        dragOver: false,
        preview: null,

        selectFile() {
            this.$refs.fileInput.click();
        },

        handleDrop(event) {
            this.dragOver = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.$refs.fileInput.files = files;
                this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                this.previewFile();
            }
        },

        previewFile() {
            const file = this.$refs.fileInput.files[0];
            if (!file) {
                this.preview = null;
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeFile() {
            this.$refs.fileInput.value = null;
            this.preview = null;
            if (this.$refs.fileInput.hasAttribute('wire:model')) {
                @this.set(this.$refs.fileInput.getAttribute('wire:model'), null);
            }
        }
    }
}
</script>
