<?php

namespace Tests\Feature\V1\Chat\Members;

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

    private string $routeName = 'api.v1.chats.members.destroy';

    private User $commonMember;

    private User $owner;

    private Chat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->commonMember = User::factory()->create();
        $this->chat = Chat::factory()->create(['is_open' => true]);
        $this->chat->users()->attach([$this->owner->id => ['role' => ChatUserRole::OWNER]]);
        $this->chat->users()->attach([$this->commonMember->id => ['role' => ChatUserRole::COMMON]]);
    }

    public function test_unauthorized()
    {
        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]));
        $response->assertUnauthorized();
    }

    public function test_users_should_be_member() {
        $notGroupMember = User::factory()->create();

        Sanctum::actingAs($notGroupMember);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]));
        $response->assertForbidden();
    }

    public function test_action_users_should_be_member() {
        $notGroupMember = User::factory()->create();

        Sanctum::actingAs($this->owner);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $notGroupMember->id]));
        $response->assertForbidden();
    }

    public function test_owner_cannot_remove_itself() {
        Sanctum::actingAs($this->owner);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->owner->id]));
        $response->assertForbidden();
    }

    public function test_success_remove_itself() {
        Sanctum::actingAs($this->commonMember);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]));
        $response->assertNoContent();
    }

    public function test_owner_can_remove_common_member() {
        Sanctum::actingAs($this->owner);

        $this->chat->users()->attach([$this->commonMember->id => ['role' => ChatUserRole::COMMON]]);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]));
        $response->assertNoContent();
    }

    public function test_owner_can_remove_admin_member() {
        Sanctum::actingAs($this->owner);

        $adminMember = User::factory()->create();
        $this->chat->users()->attach([$adminMember->id => ['role' => ChatUserRole::ADMIN]]);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $adminMember->id]));
        $response->assertNoContent();
    }

    public function test_admin_can_remove_common_user() {
        $adminMember = User::factory()->create();
        $this->chat->users()->attach([$adminMember->id => ['role' => ChatUserRole::ADMIN]]);

        Sanctum::actingAs($adminMember);

        $response = $this->deleteJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]));
        $response->assertNoContent();
    }
}
