<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'is_open' => $this->isOpen(),
            'members' => $this->whenLoaded('users_count', function () {
                return $this->users()->count();
            }),
            'avatar'  => $this->when($this->avatar, [
                'url'       => Storage::disk('avatars')->url($this->avatar),
                'file_name' => $this->avatar
            ]),
//            'owner' =>
        ];
    }
}
