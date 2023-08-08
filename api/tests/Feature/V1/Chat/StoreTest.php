<?php

namespace Tests\Feature\V1\Chat;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.store';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_unauthorized()
    {
        $data = [];

        $response = $this->postJson(route($this->routeName), $data);
        $response->assertUnauthorized();
    }

    public function test_success_without_avatar()
    {
        $data = [
            'name'    => 'test group 1',
            'is_open' => true,
            'type'    => 'group',
        ];

        Sanctum::actingAs($this->user);

        $response = $this->postJson(route($this->routeName), $data);
        $response->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_open'
            ]
        ]);

        $this->assertDatabaseHas('chats', $data);
    }

    public function test_success_with_avatar() {
        $data = [
            'name'    => 'test group 1',
            'is_open' => true,
            'type'    => 'group',
            'avatar' => UploadedFile::fake()->image('avatar1.png', 80, 80)
        ];

        Sanctum::actingAs($this->user);
        Storage::fake('avatars');

        $response = $this->postJson(route($this->routeName), $data);
        $response->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_open',
                'avatar' => [
                    'url',
                    'file_name'
                ]
            ]
        ]);

        $disk = Storage::disk('avatars');
        $disk->assertExists($response['data']['avatar']['file_name']);
        $disk->delete($disk->files());
    }
}
