<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('user_type', 'user')->pluck('id');
        $groups = Group::where('deleted_at', null)->pluck('group_id')->toArray();

        foreach($users as $user) {
            $groupMember = GroupMember::firstOrNew(['user_id' => $user]);
            if (!isset($groupMember->group_id)) {
                $groupMember->group_id = $groups[array_rand($groups)];
            }
            
            $groupMember->is_active = 1;
            $groupMember->save();
        }
    }
}
