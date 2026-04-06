<div>
    <div class="flex border-b mb-4">
        <button
            wire:click="switchTab('list')"
            class="px-4 py-2 border-b-2 cursor-pointer
                {{ $tab === 'list' ? 'border-sky-600 font-bold' : 'border-transparent' }}">
            Jurisdictions
        </button>
        <button
            wire:click="switchTab('content')"
            class="px-4 py-2 border-b-2 cursor-pointer
                {{ $tab === 'content' ? 'border-sky-600 font-bold' : 'border-transparent' }}">
            Statewide Laws
        </button>
        <button
            wire:click="switchTab('default')"
            class="px-4 py-2 border-b-2 cursor-pointer
                {{ $tab === 'default' ? 'border-sky-600 font-bold' : 'border-transparent' }}">
            Default Jurisdiction Information
        </button>
    </div>
    @if($tab === 'list')
    <!-- Filter input -->
    <div class="mb-4">
        <input type="text" class="border rounded p-2 w-96" placeholder="Filter by name..." wire:model.live="filter">
    </div>

    <!-- Sorting headers -->
    <div>
        <div class="font-bold ml-1  cursor-pointer">
            Jurisdiction
        </div>
    </div>

    <!-- Display list of jurisdictions -->
    @foreach($jurisdictions as $jurisdiction)
        <a href="{{ route('jurisdiction.show', $jurisdiction->id) }}"
        class="grid  {{ $loop->odd ? 'bg-sky-200' : 'bg-gray-100' }} cursor-pointer hover:bg-sky-300 hover:text-sky-900 transition-colors duration-200"
        wire:navigate>
            <div class="flex ml-1 items-center">
                @if($jurisdiction->sections_count)
                    <x-heroicon-s-document-text class="w-4 h-4 mr-1 text-sky-700" />
                @endif
                {{ $jurisdiction->name }}
            </div>
        </a>
    @endforeach

    <!-- Pagination links -->
    <div class="mt-4">
        {{ $jurisdictions->links() }}
    </div>

    <div class="mt-4">
        <flux:modal.trigger name="add-jurisdiction">
            <flux:button variant="primary" class="!bg-[#9adbe8] !text-[#15121b] hover:!bg-[#89c6d3]">
                Add Jurisdiction
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:modal name="add-jurisdiction" :show="$errors->has('newJurisdictionName')" focusable class="min-w-[22rem]">
        <div class="space-y-6">
            <flux:heading size="lg">Add Jurisdiction</flux:heading>

            <div class="flex flex-col gap-1">
                <flux:input
                    wire:model="newJurisdictionName"
                    placeholder="Enter jurisdiction name"
                    wire:keydown.enter="createJurisdiction"
                    autofocus
                />
                @error('newJurisdictionName')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="cancelCreate">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="createJurisdiction" variant="primary" class="!bg-[#9adbe8] !text-[#15121b] hover:!bg-[#89c6d3]">
                    Save
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
    @if($tab === 'content')
    <div class="max-w-4xl">
        <h2 class="text-lg font-bold mb-2">
            Shared Jurisdiction Page Content
        </h2>
        <livewire:jurisdiction.shared-content-editor />
    </div>
    @endif

    @if($tab === 'default')
    <div class="max-w-4xl">
        <livewire:jurisdiction.show
                :jurisdiction="$this->defaultJurisdiction"
                :editable="true"
                wire:key="default-template-editor"
            />
    </div>
    @endif
</div>
