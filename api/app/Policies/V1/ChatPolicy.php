<?php

namespace App\Policies\V1;

use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function update(User $user, Chat $chat): bool
    {
        return $chat->owner()->id === $user->id;
    }

    public function destroy(User $user, Chat $chat): bool
    {
        return $chat->owner()->id === $user->id;
    }

    public function join(User $user, Chat $chat): bool
    {
        return $chat->isOpen() && !$chat->hasMember($user->id);
    }

    public function members_index(User $user, Chat $chat): bool
    {
        return $chat->hasMember($user->id);
    }

    public function members_update(User $user, Chat $chat, User $member): bool
    {
        if (!$chat->hasMember($member->id) || !$chat->hasMember($user->id)) return false;

        $user = $chat->findMember($user->id);
        $member = $chat->findMember($member->id);

        if ($user->id === $member->id) return false;

        if ($user->pivot->hasRole(ChatUserRole::COMMON)) return false;

        if ($member->pivot->hasRole(ChatUserRole::OWNER)) return false;

        if ($user->pivot->hasRole(ChatUserRole::OWNER) && $member->pivot->hasRoles([ChatUserRole::ADMIN, ChatUserRole::COMMON])) return true;
        if ($user->pivot->hasRole(ChatUserRole::ADMIN) && $member->pivot->hasRole(ChatUserRole::COMMON)) return true;

        return false;
    }

    public function members_destroy(User $user, Chat $chat, User $member): bool
    {
        if (!$chat->hasMember($member->id) || !$chat->hasMember($user->id)) return false;

        $user = $chat->findMember($user->id);
        $member = $chat->findMember($member->id);

        if ($member->pivot->hasRole(ChatUserRole::OWNER)) return false;

        if ($user->id === $member->id) return true;

        if ($user->pivot->hasRoles([ChatUserRole::OWNER, ChatUserRole::ADMIN])) return true;

        return false;
    }

}
