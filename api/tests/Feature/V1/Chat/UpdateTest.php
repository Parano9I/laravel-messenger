<?php

namespace Tests\Feature\V1\Chat;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.update';

    private User $user;

    private Chat $chat;

    private array $chatData = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatData = [
            'name'    => 'test group 1',
            'is_open' => true,
            'type'    => 'group',
        ];

        $this->user = User::factory()->create();
        $this->chat = Chat::factory()->create($this->chatData);
        $this->chat->users()->attach([$this->user->id => ['role' => ChatUserRole::OWNER]]);
    }

    public function test_unauthorized()
    {
        $data = [];

        $response = $this->putJson(route($this->routeName, [$this->chat->id]), $data);
        $response->assertUnauthorized();
    }

    public function test_user_not_owner()
    {
        $data = [
            'name'    => $this->chatData['name'],
            'is_open' => false,
            'type'    => $this->chatData['type'],
        ];

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->chat->users()->attach([$this->user->id => ['role' => ChatUserRole::COMMON]]);

        $response = $this->putJson(route($this->routeName, [$this->chat->id]), $data);
        $response->assertForbidden();
    }

    public function test_success_without_avatar()
    {
        $data = [
            'name'    => 'test group 23',
            'is_open' => false,
            'type'    => $this->chatData['type'],
        ];

        Sanctum::actingAs($this->user);


        $response = $this->putJson(route($this->routeName, [$this->chat->id]), $data);
        $response->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_open',
            ]
        ]);

        $this->assertDatabaseHas('chats', ['id' => $this->chat->id, ...$data]);
        $this->assertDatabaseMissing('chats', [
            'id'      => $this->chat->id,
            'name'    => $this->chat->name,
            'is_open' => $this->chat->is_open
        ]);
    }

    public function test_success_with_avatar()
    {
        $data = [
            'name'    => 'test group 23',
            'is_open' => false,
            'type'    => $this->chatData['type'],
            'avatar'  => UploadedFile::fake()->image('avatar1.png', 80, 80)
        ];

        Sanctum::actingAs($this->user);
        Storage::fake('avatars');

        $response = $this->putJson(route($this->routeName, [$this->chat->id]), $data);
        $response->assertJsonStructure([
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
        $disk->assertMissing($this->chat->avatar);
        $disk->delete($disk->files());
    }
}
