<?php

namespace Tests;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public function userCreate(){
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

    }
    public function userpriveCreate(){
        Sanctum::actingAs(
            User::factory()->notAnAdmin()->create(),
            ['*']
        );

    }
}
