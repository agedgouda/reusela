<div class="bg-background min-h-screen p-6 md:p-10">
    <div class="w-full max-w-4xl flex flex-col gap-6">
        <h1 class="text-2xl font-bold">Edit User</h1>

        <form class="flex flex-col gap-6">
            <!-- Name -->
            <div>
                <flux:input
                    name="name"
                    :label="__('Name')"
                    type="text"
                    required
                    autofocus
                    wire:model.defer="name"
                />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    wire:model.defer="email"
                />
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 mt-4">
                <flux:button
                    href="/user/{{ $user->id }}"
                    wire:navigate
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800"
                >
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="save"
                >
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
