<?php

namespace Tests\Feature\V1\Chat;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.destroy';

    private User $user;

    private Chat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->chat = Chat::factory()->create();
        $this->chat->users()->attach([$this->user->id => ['role' => ChatUserRole::OWNER]]);
    }

    public function test_unauthorized()
    {
        $response = $this->deleteJson(route($this->routeName, [$this->chat->id]));
        $response->assertUnauthorized();
    }

    public function test_user_not_owner()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->chat->users()->attach([$this->user->id => ['role' => ChatUserRole::COMMON]]);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id]));
        $response->assertForbidden();
    }

    public function test_success()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id]));
        $response->assertNoContent();

        $this->assertDatabaseMissing('chats', [
            'id'      => $this->chat->id,
            'name'    => $this->chat->name,
            'is_open' => $this->chat->is_open
        ]);
    }
}
