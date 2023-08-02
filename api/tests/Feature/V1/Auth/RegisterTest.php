<?php

namespace Tests\Feature\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    private string $routName = 'api.v1.auth.register';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_success_without_avatar()
    {
        $data = [
            'name'                  => 'userTestName',
            'email'                 => 'userTest@gmail.com',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson(route($this->routName), $data);
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

        $this->assertDatabaseHas('users', [
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

    }

    public function test_success_with_avatar()
    {
        $data = [
            'name'                  => 'userTestName',
            'email'                 => 'userTest@gmail.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'avatar'                => UploadedFile::fake()->image('avatar1.png', 80, 80)
        ];

        Storage::fake('avatars');

        $response = $this->postJson(route($this->routName), $data);
        $response->assertOk()->assertJsonStructure([
            'data' => [
                'user'  => [
                    'id',
                    'name',
                    'email',
                    'avatar' => [
                        'url',
                        'file_name'
                    ]
                ],
                'token' => [
                    'type',
                    'payload'
                ]
            ]
        ]);

        $disk = Storage::disk('avatars');
        $disk->assertExists($response['data']['user']['avatar']['file_name']);
        $disk->delete($disk->files());
    }


}
