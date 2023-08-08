<?php

namespace Tests\Feature\V1\Chat;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JoinTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.join';

    private User $owner;

    private Chat $openChat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->openChat = Chat::factory()->create(['is_open' => true]);
        $this->openChat->users()->attach([$this->owner->id => ['role' => ChatUserRole::OWNER]]);
    }

    public function test_unauthorized()
    {
        $response = $this->postJson(route($this->routeName, [$this->openChat->id]), []);
        $response->assertUnauthorized();
    }

    public function test_user_should_be_is_not_member_chat()
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson(route($this->routeName, [$this->openChat->id]), []);
        $response->assertForbidden();
    }

    public function test_chat_should_be_is_open()
    {
        $newMember = User::factory()->create();
        $closeChat = Chat::factory()->create(['is_open' => false]);

        Sanctum::actingAs($newMember);

        $response = $this->postJson(route($this->routeName, [$closeChat]), []);
        $response->assertForbidden();
    }

    public function test_success()
    {
        $newMember = User::factory()->create();

        Sanctum::actingAs($newMember);

        $response = $this->postJson(route($this->routeName, [$this->openChat]), []);
        $response->assertAccepted()->assertJson(['message' => 'You have joined the group']);

        $this->assertDatabaseHas('chats_users', [
           'user_id' => $newMember->id,
           'chat_id' => $this->openChat->id,
           'role' => ChatUserRole::COMMON
        ]);
    }

}
