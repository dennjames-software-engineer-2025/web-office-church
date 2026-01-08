<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // ==================================
        // Deklarasi Permissions
        // ==================================
        $permissions = [ 

            // DOcument Permissions
            'document.view_any',
            'document.view_own_bidang',
            'document.create',
            'document.update_own',
            'document.delete_own',
            'document.restore',
            'document.force_delete',

            // User & Approval Permissions
            'user.approve_registration',
            'user.manage_tim_inti',
            'user.manage_tim_bidang',

            // Struktur Organisasi
            'structure.manage_bidang',
            'structure.manage_sie',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ==================================
        // Deklarasi Roles
        // ==================================

        $superadmin = Role::firstOrCreate(['name' => 'super_admin']);
        $timInti    = Role::firstOrCreate(['name' => 'tim_inti']);
        $timBidang  = Role::firstOrCreate(['name' => 'tim_bidang']);

        // ==================================
        // Assign Permissions to Each Roles
        // ==================================

        // Super Admin mendapatkan semua Permissions
        $superadmin->syncPermissions(Permission::all());

        // Permissions untuk Tim Inti
        $timInti->syncPermissions([
            'document.view_any',
            'document.create',
            'document.update_own',
            'document.delete_own',
            'document.restore',
            'user.approve_registration',
            'structure.manage_bidang',
            'structure.manage_sie',
        ]);

        // Permissions untuk Tim Bidang
        $timBidang->syncPermissions([
            'document.view_own_bidang',
            'document.create',
            'document.update_own',
            'document.delete_own',
        ]);
    }
}
