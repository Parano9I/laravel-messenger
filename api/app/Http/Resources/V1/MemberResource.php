<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'avatar' => $this->when($this->avatar, [
                'url'       => Storage::disk('avatars')->url($this->avatar),
                'file_name' => $this->avatar
            ]),
            'role'   => $this->pivot->role
        ];
    }
}
