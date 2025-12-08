<div>

    <!-- Filter input -->
    <div class="mb-4">
        <input type="text" class="border rounded p-2 w-96" placeholder="Filter by name..." wire:model.live="filter">
    </div>

    <!-- Sorting headers -->
    <div class="grid grid-cols-2">
        <div class="font-bold cursor-pointer" wire:click="sortBy('name')">
            Name
            @if ($sortField === 'name')
                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
            @endif
        </div>
    </div>

    <!-- Display list of jurisdictions -->
    @foreach($jurisdictions as $jurisdiction)
        <a href="{{ route('jurisdiction.show', $jurisdiction->id) }}"
        class="grid grid-cols-2 {{ $loop->odd ? 'bg-gray-200' : '' }} cursor-pointer hover:bg-gray-300"
        wire:navigate>
            <div>{{ $jurisdiction->name }}</div>
        </a>
    @endforeach

    <!-- Pagination links -->
    <div class="mt-4">
        {{ $jurisdictions->links() }}
    </div>
</div>
