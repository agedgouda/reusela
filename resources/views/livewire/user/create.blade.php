<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Create a user')"
            :description="__('Enter user details below')"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit.prevent="createUser('redirect')" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
                wire:model.defer="name"
            />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <!-- Email -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                wire:model.defer="email"
            />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
                wire:model.defer="password"
            />
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
                wire:model.defer="password_confirmation"
            />

            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <!-- Save button: redirects to /users -->
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                    {{ __('Save') }}
                </flux:button>

                <!-- Save & Add New: calls Livewire method with parameter -->
                <flux:button
                    type="button"
                    class="w-full sm:w-auto"
                    wire:click="createUser('reset')"
                >
                    {{ __('Save & Add New') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth>
