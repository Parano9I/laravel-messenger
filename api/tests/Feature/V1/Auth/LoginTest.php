<?php

namespace Tests\Feature\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private string $routName = 'api.v1.auth.login';

    private User $user;

    private array $postData = [
      'email' => 'testUser@gmail.com',
      'password' => 'password'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create($this->postData);
    }

    public function test_success() {
        $response = $this->postJson(route($this->routName), $this->postData);
        $response->assertOk()->assertJsonStructure([
            'data' => [
                'user'  => [
                    'id',
                    'name',
                    'email',
                ],
                'token' => [
                    'type',
                    'payload'
                ]
            ]
        ]);
    }
}
