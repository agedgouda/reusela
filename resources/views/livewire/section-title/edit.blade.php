<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Section Title</h1>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="block font-semibold mb-1">Title</label>
            <input type="text"
                   wire:model="title"
                   class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-500">
            @error('title') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <x-file-upload label="Replace Icon" wire:model="icon" :current-file="$sectionTitle->icon" />

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Save
        </button>
    </form>

    <a href="{{ route('section-title.index') }}" class="text-blue-600 mt-4 inline-block">← Back</a>
</div>
