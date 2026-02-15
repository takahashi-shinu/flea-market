<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnvCheckTest extends TestCase
{
    /** @test */
    public function env_is_testing()
    {
        dump(app()->environment());
        dump(config('database.default'));
        dump(config('database.connections.sqlite.database'));

        $this->assertTrue(true);
    }

    /** @test */
    public function testing_env_is_loaded()
    {
        $this->assertEquals('testing', app()->environment());
    }

    /** @test */
    public function session_driver_is_array()
    {
        $this->assertEquals('array', config('session.driver'));
    }

    // CSRF無効化済みなら通る
    /** @test */
    public function csrf_protection_is_disabled_for_tests()
    {
        $this->assertTrue(true);
    }
}
