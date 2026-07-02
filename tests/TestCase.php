<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $installedPath = storage_path('installed');
        if (!file_exists($installedPath)) {
            @file_put_contents($installedPath, 'installed');
        }
    }
}

