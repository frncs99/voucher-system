<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            [
                'email' => 'superadmin@example.com'
            ],
            [
                'user_type' => 'super_admin',
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        
        $user->assignRole('super_admin');
    }
}
