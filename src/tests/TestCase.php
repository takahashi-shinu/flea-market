<?php


namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // FeatureテストではCSRFを完全無効化
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }
}
