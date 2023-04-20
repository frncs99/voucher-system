<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate(['name' => 'create-voucher']);
        Permission::updateOrCreate(['name' => 'delete-voucher']);
        Permission::updateOrCreate(['name' => 'view-voucher']);
        Permission::updateOrCreate(['name' => 'view-users']);
        Permission::updateOrCreate(['name' => 'assign-group-member']);
        Permission::updateOrCreate(['name' => 'assign-group-admin']);
        Permission::updateOrCreate(['name' => 'export']);
        Permission::updateOrCreate(['name' => 'group-crud']);

        $userRole = Role::updateOrCreate(['name' => 'user']);
        $groupAdminRole = Role::updateOrCreate(['name' => 'group_admin']);
        $superAdminRole = Role::updateOrCreate(['name' => 'super_admin']);

        $userRole->givePermissionTo([
            'create-voucher',
            'delete-voucher',
            'view-voucher',
        ]);

        $groupAdminRole->givePermissionTo([
            'view-voucher',
            'view-users',
            'assign-group-member',
            'export',
        ]);

        $superAdminRole->givePermissionTo([
            'view-voucher',
            'view-users',
            'assign-group-member',
            'assign-group-admin',
            'export',
            'group-crud',
        ]);
    }
}
