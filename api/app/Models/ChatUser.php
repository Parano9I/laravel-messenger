<?php

namespace App\Models;

use App\Enums\ChatUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatUser extends Pivot
{
    use HasFactory;

    protected $table = 'chat_users';

    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
    ];

    protected $casts = [
        'role' => ChatUserRole::class
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasRole(ChatUserRole $role): bool
    {
        return $this->role === $role;
    }

    public function hasRoles(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}
