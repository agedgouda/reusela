<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

use App\Livewire\Jurisdiction\Index as JurisdictionIndex;
use App\Livewire\Jurisdiction\Show as JurisdictionShow;

use App\Livewire\SectionTitle\Index as SectionTitleIndex;
use App\Livewire\SectionTitle\Show as SectionTitleShow;
use App\Livewire\SectionTitle\Create as SectionTitleCreate;
use App\Livewire\SectionTitle\Edit as SectionTitleEdit;

use App\Livewire\Search\Index as SearchIndex;

Route::get('/', function () {
    return view('welcome');
})->name('home');

//Route::get('/search', SearchIndex::class)->name('search.index');
Route::get('/search', function () {
    return view('search-page'); // a Blade file you create
})->name('search.index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

        Route::prefix('jurisdictions')->group(function () {
            Route::get('/', JurisdictionIndex::class)->name('jurisdiction.index');
            Route::get('/{jurisdiction}', JurisdictionShow::class)->name('jurisdiction.show');
        });

        Route::prefix('section-title')->name('section-title.')->group(function () {
            Route::get('/', SectionTitleIndex::class)->name('index');
            Route::get('/create', SectionTitleCreate::class)->name('create');
            Route::get('/{sectionTitle}', SectionTitleShow::class)->name('show');
            Route::get('/{sectionTitle}/edit', SectionTitleEdit::class)->name('edit');
        });

});
