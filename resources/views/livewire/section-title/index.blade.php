<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Section Titles</h1>

    <a href="{{ route('section-title.create') }}"
       class="bg-blue-600 text-white px-3 py-1 rounded">
        Create New
    </a>

    <!-- Sortable container: rendered server-side so keys/data are scalar -->
    <div
        id="section-title-sortable"
        class="mt-6 space-y-2"
        x-data
        x-init="
            // wait one tick to ensure Alpine/Livewire are ready
            setTimeout(() => {
                if (!window.Sortable) {
                    console.warn('Sortable.js not loaded.');
                    return;
                }

                // destroy previous instance if present
                if ($el._sortableInstance) {
                    $el._sortableInstance.destroy();
                }

                $el._sortableInstance = new Sortable($el, {
                    animation: 150,
                    handle: '.handle',
                    onEnd: function() {
                        // gather ordered ids (as strings)
                        const ordered = Array.from($el.children).map(ch => ch.dataset.id);
                        // call Livewire method
                        $wire.updateSortOrder(ordered);
                    }
                });
            }, 0);
        "
    >

        @foreach($titles as $title)
            <div
                data-id="{{ $title['id'] }}"
                wire:key="st-{{ $title['id'] }}"
                class="p-3 border rounded flex items-center justify-between bg-white shadow-sm"
            >
                <div class="handle cursor-move pr-4 text-gray-400 hover:text-gray-600">☰</div>

                <div class="flex-1">
                    <div class="font-medium"> {{ $title['sort_order'] }}. {{ $title['title'] }}</div>
                </div>

                <div class="space-x-2">
                    <a href="{{ route('section-title.show', $title['id']) }}" class="text-blue-600">View</a>
                    <a href="{{ route('section-title.edit', $title['id']) }}" class="text-green-600">Edit</a>
                </div>
            </div>
        @endforeach

    </div>
</div>
