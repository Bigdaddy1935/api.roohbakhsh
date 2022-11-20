<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     * giving url status code test
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }
}
