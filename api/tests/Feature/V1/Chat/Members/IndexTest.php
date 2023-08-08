<?php

namespace Tests\Feature\V1\Chat\Members;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.members.index';

    private User $member;

    private Chat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create();
        $this->member = User::factory()->create();
        $this->chat = Chat::factory()->create(['is_open' => true]);
        $this->chat->users()->attach([$owner->id => ['role' => ChatUserRole::OWNER]]);
        $this->chat->users()->attach([$this->member->id => ['role' => ChatUserRole::COMMON]]);
    }

    public function test_unauthorized()
    {
        $response = $this->getJson(route($this->routeName, [$this->chat->id]));
        $response->assertUnauthorized();
    }

    public function test_user_should_be_chat_member() {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(route($this->routeName, [$this->chat->id]));
        $response->assertForbidden();
    }

    public function test_success() {
        Sanctum::actingAs($this->member);

        $response = $this->getJson(route($this->routeName, [$this->chat->id]));
        $response->assertOk();
    }
}
