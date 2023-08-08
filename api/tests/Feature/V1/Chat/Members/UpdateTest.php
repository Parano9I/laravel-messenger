<?php

namespace Tests\Feature\V1\Chat\Members;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private string $routeName = 'api.v1.chats.members.update';

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
        $response = $this->putJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]), []);
        $response->assertUnauthorized();
    }

    public function test_users_should_be_member()
    {
        $notGroupMember = User::factory()->create();

        Sanctum::actingAs($notGroupMember);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $this->commonMember->id]), ['role' => ChatUserRole::COMMON->value]);
        $response->assertForbidden();
    }

    public function test_action_users_should_be_member()
    {
        $notGroupMember = User::factory()->create();

        Sanctum::actingAs($this->owner);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $notGroupMember->id]), ['role' => ChatUserRole::COMMON->value]);
        $response->assertForbidden();
    }

    public function test_cannot_update_itself()
    {

        $adminMember = User::factory()->create();
        $this->chat->users()->attach([$adminMember->id => ['role' => ChatUserRole::ADMIN]]);

        Sanctum::actingAs($adminMember);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $adminMember->id]), ['role' => ChatUserRole::COMMON->value]);
        $response->assertForbidden();
    }

    public function test_common_member_cannot_update()
    {
        $otherCommonMember = User::factory()->create();
        $this->chat->users()->attach([$otherCommonMember->id => ['role' => ChatUserRole::COMMON]]);

        Sanctum::actingAs($this->commonMember);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $otherCommonMember->id]), ['role' => ChatUserRole::ADMIN->value]);
        $response->assertForbidden();
    }

    public function test_cannot_update_owner()
    {
        $adminMember = User::factory()->create();
        $this->chat->users()->attach([$adminMember->id => ['role' => ChatUserRole::ADMIN]]);

        Sanctum::actingAs($this->commonMember);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $adminMember->id]), ['role' => ChatUserRole::ADMIN->value]);
        $response->assertForbidden();
    }

    public function roles_permissions_casts(): array
    {
        return [
            'owner_can_update_admin'       => [
                ['user_role' => 'admin', 'action_user_role' => 'owner', 'data' => [
                    'role' => 'common'
                ]]
            ],
            'owner_can_update_common_user' => [
                ['user_role' => 'common', 'action_user_role' => 'owner', 'data' => [
                    'role' => 'admin'
                ]]
            ],
            'admin_can_update_common_user' => [
                ['user_role' => 'common', 'action_user_role' => 'owner', 'data' => [
                    'role' => 'admin'
                ]]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider roles_permissions_casts
     */
    public function test_action_user_can_update_user($data)
    {
        $actionUser = User::factory()->create();
        $this->chat->users()->attach([$actionUser->id => ['role' => ChatUserRole::from($data['action_user_role'])]]);

        $user = User::factory()->create();
        $this->chat->users()->attach([$user->id => ['role' => ChatUserRole::from($data['user_role'])]]);

        Sanctum::actingAs($actionUser);

        $response = $this->putJson(route($this->routeName, [$this->chat->id, $user->id]), $data['data']);
        $response->assertAccepted();
    }
}
