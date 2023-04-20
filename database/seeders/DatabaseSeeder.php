<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleAndPermissionSeeder::class,
            SuperAdminUserSeeder::class,
            GroupAdminUserSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            GroupMemberSeeder::class,
            GroupAdminSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}
