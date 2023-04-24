<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('user_type', 'group_admin')->pluck('id');
        $groups = Group::pluck('group_id')->toArray();

        foreach($users as $user) {
            GroupAdmin::updateOrCreate(
                [
                    'user_admin_id' => $user,
                    'group_id' => $groups[array_rand($groups)]
                ],
                [
                    'is_active' => 1
                ]
            );
        }
    
        foreach($users as $user) {
            GroupAdmin::updateOrCreate(
                [
                    'user_admin_id' => $user,
                    'group_id' => $groups[array_rand($groups)]
                ],
                [
                    'is_active' => 1
                ]
            );
        }
    }
}
