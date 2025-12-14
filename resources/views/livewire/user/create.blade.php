<div class="bg-background min-h-screen md:p-10">
    <div class="w-full  flex flex-col gap-6">
        <!-- Page Title -->
        <h1 class="text-2xl font-bold mt-6">Create User</h1>

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" class="mb-4" />

        <!-- Name -->
        <div>
            <flux:input
                name="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Name')"
                wire:model.defer="name"
            />

        </div>

        <!-- Email -->
        <div>
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                wire:model.defer="email"
            />
        </div>

        <!-- Password -->
        <div>
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
        </div>

        <!-- Confirm Password -->
        <div>
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
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 mt-4 justify-end">
            <flux:button
                type="button"
                variant="primary"
                class="w-full sm:w-auto"
                wire:click="saveAndRedirect"
            >
                {{ __('Save') }}
            </flux:button>

            <flux:button
                type="button"
                class="w-full sm:w-auto bg-gray-300 hover:bg-gray-400 text-gray-800"
                wire:click="saveAndAddNew"
            >
                {{ __('Save & Add New') }}
            </flux:button>
        </div>
    </div>
</div>
