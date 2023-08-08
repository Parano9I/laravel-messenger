<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ChatUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Chat\Member\UpdateRequest;
use App\Http\Resources\V1\MemberResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatMemberController extends Controller
{
    public function index(Chat $chat)
    {
        $this->authorize('members_index', $chat);

        $members = $chat->users()->paginate(10);

        return MemberResource::collection($members);
    }

    public function update(UpdateRequest $request, Chat $chat, User $member)
    {
        $this->authorize('members_update', [$chat, $member]);

        $request->validated();

        $chat->users()->updateExistingPivot($member->id, ['role' => ChatUserRole::from($request->string('role'))]);

        return response()->json([
            'message' => 'Role updated.'
        ], Response::HTTP_ACCEPTED);
    }

    public function destroy(Chat $chat, User $member)
    {
        $this->authorize('members_destroy', [$chat, $member]);

        $chat->users()->detach($member);

        return response(status: Response::HTTP_NO_CONTENT);
    }
}
