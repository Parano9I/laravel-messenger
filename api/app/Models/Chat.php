<?php

namespace App\Models;

use App\Enums\ChatType;
use App\Enums\ChatUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'is_open',
        'type'
    ];

    protected $casts = [
        'type'    => ChatType::class,
        'is_open' => 'bool'
    ];

    public function isOpen(): bool
    {
        return $this->is_open;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chats_users')->withPivot('role')
            ->withTimestamps()->using(ChatUser::class);
    }

    public function owner()
    {
        return $this->users()
            ->where('role', ChatUserRole::OWNER)
            ->first();
    }

    public function hasMember(int $userId) {
        return  $this->users()->where('user_id', $userId)->exists();
    }

    public function findMember(int $memberId): ?User {
        return $this->users()->where('user_id', $memberId)->first();
    }
}
