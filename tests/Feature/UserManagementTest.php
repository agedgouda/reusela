<?php

declare(strict_types=1);

use App\Livewire\User\Create;
use App\Livewire\User\Edit;
use App\Livewire\User\Index;
use App\Livewire\User\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
});

it('requires login for every user page', function (string $routeName, bool $needsUser) {
    $parameters = $needsUser ? [$this->admin] : [];

    $this->get(route($routeName, $parameters))->assertRedirect(route('login'));
})->with([
    'index' => ['user.index', false],
    'create' => ['user.create', false],
    'show' => ['user.show', true],
    'edit' => ['user.edit', true],
]);

it('renders every user page for a logged-in user', function (string $routeName, bool $needsUser) {
    $parameters = $needsUser ? [$this->admin] : [];

    $this->actingAs($this->admin)->get(route($routeName, $parameters))->assertSuccessful();
})->with([
    'index' => ['user.index', false],
    'create' => ['user.create', false],
    'show' => ['user.show', true],
    'edit' => ['user.edit', true],
]);

it('binds the user form fields with plain wire:model', function (string $routeName, bool $needsUser) {
    $parameters = $needsUser ? [$this->admin] : [];

    $this->actingAs($this->admin)
        ->get(route($routeName, $parameters))
        ->assertSee('wire:model="name"', false)
        ->assertSee('wire:model="email"', false)
        ->assertDontSee('wire:model.defer', false);
})->with([
    'create' => ['user.create', false],
    'edit' => ['user.edit', true],
]);

it('lists all users', function () {
    $other = User::factory()->create(['name' => 'Another Person']);

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->assertSee($this->admin->name)
        ->assertSee('Another Person');
});

it('creates a user and redirects to the list', function () {
    Livewire::actingAs($this->admin)
        ->test(Create::class)
        ->set('name', 'New Admin')
        ->set('email', 'new@example.com')
        ->set('password', 'secret-password')
        ->set('password_confirmation', 'secret-password')
        ->call('saveAndRedirect')
        ->assertHasNoErrors()
        ->assertRedirect(route('user.index'));

    $user = User::where('email', 'new@example.com')->sole();

    expect($user->name)->toBe('New Admin')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

it('creates a user and returns to a blank form', function () {
    Livewire::actingAs($this->admin)
        ->test(Create::class)
        ->set('name', 'Second Admin')
        ->set('email', 'second@example.com')
        ->set('password', 'secret-password')
        ->set('password_confirmation', 'secret-password')
        ->call('saveAndAddNew')
        ->assertHasNoErrors()
        ->assertRedirect(route('user.create'))
        ->assertSet('name', '');

    expect(User::where('email', 'second@example.com')->exists())->toBeTrue();
});

it('validates new users', function (array $input, string $errorField) {
    Livewire::actingAs($this->admin)
        ->test(Create::class)
        ->fill(array_merge([
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ], $input))
        ->call('saveAndRedirect')
        ->assertHasErrors($errorField);

    expect(User::count())->toBe(1);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'different-password'], 'password'],
]);

it('rejects a duplicate email when creating', function () {
    Livewire::actingAs($this->admin)
        ->test(Create::class)
        ->set('name', 'Duplicate')
        ->set('email', $this->admin->email)
        ->set('password', 'secret-password')
        ->set('password_confirmation', 'secret-password')
        ->call('saveAndRedirect')
        ->assertHasErrors(['email' => 'unique']);
});

it('updates a user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Edit::class, ['user' => $user])
        ->assertSet('name', $user->name)
        ->set('name', 'Renamed')
        ->set('email', 'renamed@example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('user.show', $user));

    expect($user->fresh())
        ->name->toBe('Renamed')
        ->email->toBe('renamed@example.com');
});

it('keeps the same email when editing without changing it', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Edit::class, ['user' => $user])
        ->set('name', 'Only Name Changed')
        ->call('save')
        ->assertHasNoErrors();
});

it('rejects another user\'s email when editing', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Edit::class, ['user' => $user])
        ->set('email', $this->admin->email)
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

it('confirms before deleting a user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Show::class, ['user' => $user])
        ->assertSet('confirmingDelete', false)
        ->call('confirmDelete')
        ->assertSet('confirmingDelete', true)
        ->call('cancelDelete')
        ->assertSet('confirmingDelete', false);

    expect($user->fresh())->not->toBeNull();
});

it('deletes a user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Show::class, ['user' => $user])
        ->call('deleteUser')
        ->assertRedirect(route('user.index'));

    expect($user->fresh())->toBeNull();
});
