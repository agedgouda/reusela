<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Create Section Title</h1>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="block font-semibold mb-1">Title</label>
            <input type="text"
                   wire:model="title"
                   class="border p-2 w-full rounded focus:ring-2 focus:ring-green-500">
            @error('title') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <x-file-upload label="Icon Image" wire:model="icon" />

        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            Save
        </button>
    </form>

    <a href="{{ route('section-title.index') }}" class="text-blue-600 mt-4 inline-block">← Back</a>
</div>
