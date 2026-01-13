<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // =========================
        // Permissions (ringkas tapi lengkap untuk alur Program/Proposal baru)
        // =========================
        $permissions = [
            // Users & Masterdata
            'users.manage',
            'masterdata.manage',

            // Program
            'program.create',
            'program.view',
            'program.update_pending',
            'program.delete_pending',
            'program.approve',          // ketua / wakil ketua (sesuai target kedudukan via policy)
            'program.status.update',    // ubah status setelah approve

            // Proposal
            'proposal.upload',          // upload berkali-kali
            'proposal.delete_rejected', // opsi B: boleh delete saat review/ditolak (nanti di policy)
            'proposal.approve',         // ketua / wakil ketua (sesuai target kedudukan via policy)
            'proposal.view_approved',   // bendahara view-only setelah approved

            // Notulensi & Files
            'notulensi.manage',
            'notulensi.view',
            'files.manage',
            'files.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // =========================
        // Roles
        // =========================
        $roles = [
            'super_admin',
            'ketua',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'ketua_bidang',
            'ketua_sie',
            'ketua_lingkungan',
            'wakil_ketua_lingkungan',
            'anggota_komunitas',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // =========================
        // Assign Permissions
        // =========================

        Role::findByName('super_admin', 'web')->syncPermissions(Permission::all());

        Role::findByName('ketua', 'web')->syncPermissions([
            'program.view',
            'program.approve',
            'program.status.update',
            'proposal.approve',
            'proposal.view_approved',
            'files.view',
            'notulensi.view',
        ]);

        Role::findByName('wakil_ketua', 'web')->syncPermissions([
            'program.view',
            'program.approve',
            'program.status.update',
            'proposal.approve',
            'proposal.view_approved',
            'files.view',
            'notulensi.view',
        ]);

        Role::findByName('sekretaris', 'web')->syncPermissions([
            'program.view',
            'files.manage',
            'files.view',
            'notulensi.manage',
            'notulensi.view',
        ]);

        Role::findByName('bendahara', 'web')->syncPermissions([
            'program.view',
            'proposal.view_approved', // view-only untuk pencatatan
            'files.view',
            'notulensi.view',
        ]);

        Role::findByName('ketua_bidang', 'web')->syncPermissions([
            'program.create',
            'program.view',
            'program.update_pending',
            'program.delete_pending',
            'proposal.upload',
            'proposal.delete_rejected',
            'files.view',
            'notulensi.view',
        ]);

        Role::findByName('ketua_sie', 'web')->syncPermissions([
            'program.create',
            'program.view',
            'program.update_pending',
            'program.delete_pending',
            'proposal.upload',
            'proposal.delete_rejected',
            'files.view',
            'notulensi.view',
        ]);

        Role::findByName('ketua_lingkungan', 'web')->syncPermissions([
            'program.view',
            'files.view',
            'notulensi.view',
            // Ketua Lingkungan tidak approve (sesuai brief: larinya ke Ketua/Wakil Ketua DPP Inti)
        ]);

        Role::findByName('wakil_ketua_lingkungan', 'web')->syncPermissions([
            'program.view',
            'files.view',
            'notulensi.view',
            // Ketua Lingkungan tidak approve (sesuai brief: larinya ke Ketua/Wakil Ketua DPP Inti)
        ]);

        Role::findByName('anggota_komunitas', 'web')->syncPermissions([
            'program.create',
            'program.view',
            'proposal.upload',
            'proposal.delete_rejected',
            'files.view',
            'notulensi.view',
        ]);
    }
}
