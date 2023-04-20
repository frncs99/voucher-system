<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GroupAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 2) as $num) {
            $faker = Factory::create();

            $user = User::updateOrCreate(
                [
                    'email' => 'groupadmin_0' . $num . '@example.com'
                ],
                [
                    'user_type' => 'group_admin',
                    'name' => $faker->name,
                    'password' => Hash::make('password'),
                ]
            );
            
            $user->assignRole('group_admin');
        }
    }
}
