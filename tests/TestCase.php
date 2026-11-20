<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private static ?string $throwawayAppKey = null;

    protected function setUp(): void
    {
        $this->useThrowawayAppKey();

        parent::setUp();
    }

    /**
     * The random APP_KEY generated for this test run. It is never written to disk.
     */
    public static function throwawayAppKey(): string
    {
        return self::$throwawayAppKey ??= 'base64:'.base64_encode(random_bytes(32));
    }

    /**
     * Sets APP_KEY before the app boots. Laravel never overwrites an environment
     * variable that is already set, so this key wins over the one in .env.
     */
    private function useThrowawayAppKey(): void
    {
        $appKey = self::throwawayAppKey();

        putenv('APP_KEY='.$appKey);
        $_ENV['APP_KEY'] = $appKey;
        $_SERVER['APP_KEY'] = $appKey;
    }
}
