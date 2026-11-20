<?php

use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

it('runs with the throwaway key generated for this test run', function () {
    expect(config('app.key'))->toBe(TestCase::throwawayAppKey());
});

it('generates a valid 32-byte key', function () {
    $key = TestCase::throwawayAppKey();

    expect($key)->toStartWith('base64:')
        ->and(strlen(base64_decode(substr($key, 7), true)))->toBe(32);
});

it('can encrypt and decrypt with the throwaway key', function () {
    expect(Crypt::decryptString(Crypt::encryptString('secret')))->toBe('secret');
});
