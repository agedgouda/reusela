<div>
    <div class="flex justify-end mb-5">
        <flux:link :href="url('/user/create')">
            <flux:button variant="primary" class="bg-blue-800 hover:bg-blue-400">
                {{ __('Create User') }}
            </flux:button>
        </flux:link>
    </div>

    @foreach($users as $user)
        <a href="{{ route('user.show', $user->id) }}"
        class="grid grid-cols-2 {{ $loop->odd ? 'bg-sky-200' : 'bg-gray-100' }} flex ml-1 items-center cursor-pointer hover:bg-sky-300 hover:text-sky-900 transition-colors duration-200"
        wire:navigate>
            <div>
                {{ $user->name }}
            </div>
            <div>
                {{ $user->email }}
            </div>
        </a>
    @endforeach
</div>
