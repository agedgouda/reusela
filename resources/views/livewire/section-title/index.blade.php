<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Section Titles</h1>

    <a href="{{ route('section-title.create') }}"
       class="bg-blue-600 text-white px-3 py-1 rounded">
        Create New
    </a>

    {{-- Drag-and-drop ordering uses Livewire's built-in wire:sort (no external script). --}}
    <div id="section-title-sortable" class="mt-6 space-y-2" wire:sort="moveTitle">

        @foreach($titles as $title)
            <div
                wire:sort:item="{{ $title['id'] }}"
                wire:key="st-{{ $title['id'] }}"
                class="p-3 border rounded flex items-center justify-between bg-white shadow-sm"
            >
                <div wire:sort:handle class="cursor-move pr-4 text-gray-400 hover:text-gray-600">☰</div>

                <div class="flex-1">
                    <div class="flex items-center font-medium space-x-2">
                        <span>{{ $title['sort_order'] }}.</span>
                        @if ($title['icon']) <img class="h-5" src="{{ Storage::url('icons/' .$title['icon']) }}" alt=""> @endif
                        <span>{{ $title['title'] }}</span>
                    </div>
                </div>

                <div class="space-x-2">
                    <a href="{{ route('section-title.edit', $title['id']) }}" class="text-green-600">Edit</a>
                </div>
            </div>
        @endforeach

    </div>
</div>
