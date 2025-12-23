<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">View Section Title</h1>

    <p class="flex items-center font-medium space-x-2 text-lg mb-4">
        @if ($sectionTitle->icon)<img src="{{ Storage::url('icons/' .$sectionTitle->icon ) }}">@endif
        <span>{{ $sectionTitle->title }}</span>
    </p>

    <a href="{{ route('section-title.index') }}" class="text-blue-600">← Back to list</a>
</div>
