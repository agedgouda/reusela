<div class="bg-background min-h-screen p-6 md:p-10">
    <a href="/user" class="hover:text-blue-300 text-blue-800 mb-5"><- Back</a>
    <div class="w-full max-w-4xl flex flex-col ">
        <h1 class="text-2xl font-bold">User Details</h1>

        <div class="bg-white dark:bg-neutral-900 rounded-lg p-6 shadow flex flex-col gap-4">
            <div>
                <div class="text-sm text-gray-500">Name</div>
                <div class="text-lg font-medium">{{ $user->name }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Email</div>
                <div class="text-lg font-medium">{{ $user->email }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
            <flux:button
                href="{{ route('user.edit', $user) }}"
                wire:navigate
                variant="primary"
                class="w-full sm:w-auto"
            >
                {{ __('Edit') }}
            </flux:button>

            <flux:button
                type="button"
                variant="danger"
                class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white"
                wire:click="confirmDelete"
            >
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">
                    Delete User
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete <strong>{{ $user->name }}</strong>?
                    This action cannot be undone.
                </p>

                <div class="flex justify-end gap-3">
                    <flux:button
                        type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800"
                        wire:click="cancelDelete"
                    >
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button
                        type="button"
                        variant="danger"
                        class="bg-red-600 hover:bg-red-700 text-white"
                        wire:click="deleteUser"
                    >
                        {{ __('Delete') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>

