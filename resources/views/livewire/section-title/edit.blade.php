<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Section Title</h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block font-semibold">Title</label>
            <input type="text" wire:model="title" class="border p-2 w-full rounded">
            @error('title') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>

    <a href="{{ route('section-title.index') }}" class="text-blue-600 mt-4 inline-block">← Back</a>
</div>
