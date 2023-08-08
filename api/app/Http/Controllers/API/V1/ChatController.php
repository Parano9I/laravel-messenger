<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ChatType;
use App\Enums\ChatUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Chat\StoreRequest;
use App\Http\Requests\V1\Chat\UpdateRequest;
use App\Http\Resources\V1\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {

    }

    public function show()
    {
    }

    public function store(StoreRequest $request)
    {
        $request->validated();

        $owner = $request->user();

        $chat = new Chat();
        $chat->name = $request->get('name');
        $chat->is_open = $request->boolean('is_open');
        $chat->type = ChatType::from($request->get('type'));

        if ($request->hasFile('avatar')) {
            $fileName = Storage::disk('avatars')->put(null, $request->file('avatar'));
            $chat->avatar = $fileName;
        }

        $chat->save();
        $chat->users()->attach([$owner->id => ['role' => ChatUserRole::OWNER]]);

        return new ChatResource($chat);
    }

    public function update(UpdateRequest $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $request->validated();

        $chat->name = $request->get('name');
        $chat->is_open = $request->boolean('is_open');
        $chat->type = ChatType::from($request->get('type'));

        if ($request->hasFile('avatar')) {
            $oldAvatarFileName = $chat->avatar;

            $disk = Storage::disk('avatars');
            $newAvatarFileName = $disk->put(null, $request->file('avatar'));
            $chat->avatar = $newAvatarFileName;

            if (!is_null($oldAvatarFileName)) $disk->delete($oldAvatarFileName);
        }

        $chat->save();

        return new ChatResource($chat);
    }

    public function destroy(Chat $chat)
    {
        $this->authorize('destroy', $chat);

        $chat->delete();

        return response(status: Response::HTTP_NO_CONTENT);
    }

    public function join(Request $request, Chat $chat)
    {
        $this->authorize('join', $chat);

        $user = $request->user();

        $chat->users()->attach([$user->id => ['role' => ChatUserRole::COMMON]]);

        return response()->json([
            'message' => 'You have joined the group'
        ], Response::HTTP_ACCEPTED);
    }
}
