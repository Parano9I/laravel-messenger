<?php

namespace Database\Seeders;

use App\Enums\ChatType;
use App\Enums\ChatUserRole;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Chat::all() as $chat) {
            $chatOwner = User::inRandomOrder()->first();
            $chat->users()->attach([$chatOwner->id => ['role' => ChatUserRole::OWNER]]);

            if (!$chat->isOpen() && $chat->type === ChatType::PERSONAL) {
                $member = User::inRandomOrder()->first();
                $chat->users()->attach([$member->id => ['role' => ChatUserRole::OWNER]]);
            } else {
                $members = User::inRandomOrder()->limit(5)->get();
                $chat->users()->sync($members->pluck('id')->toArray());
            }
        }
    }
}
