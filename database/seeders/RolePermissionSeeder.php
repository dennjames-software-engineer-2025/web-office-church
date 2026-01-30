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
        // Permissions
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
            'program.approve',
            'program.status.update',

            // Proposal
            'proposal.upload',
            'proposal.delete_rejected',
            'proposal.approve',
            'proposal.view_approved',

            // Notulensi & Files
            'notulensi.manage',
            'notulensi.view',
            'files.manage',
            'files.view',

            // CRUD Bidang & Sie
            'bidang.manage',
            'sie.manage',

            // Documents
            'documents.view',
            'documents.create',
            'documents.delete',

            // Announcement / Pengumuman
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
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
            'sekretariat', 
            
            // lingkungan - wilayah
            'fasilitator_wilayah',
            'sekretaris_wilayah',
            'bendahara_wilayah',
            'sie_biak_wilayah',
            'sie_omk_wilayah',
            'sie_keluarga_wilayah',
            'sie_lansia_wilayah',
            'sie_tatib_kolektan_wilayah',

            // lingkungan - lingkungan
            'sekretaris_lingkungan',
            'bendahara_lingkungan',
            'seksi_liturgi',
            'seksi_katekese',
            'seksi_kerasulan_kitab_suci',
            'seksi_sosial',
            'seksi_pengabdian_masyarakat',
            'pelayanan_kematian',
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
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
        ]);

        Role::findByName('wakil_ketua', 'web')->syncPermissions([
            'program.view',
            'program.approve',
            'program.status.update',
            'proposal.approve',
            'proposal.view_approved',
            'files.view',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
        ]);

        Role::findByName('sekretaris', 'web')->syncPermissions([
            'program.view',
            'files.manage',
            'files.view',
            'notulensi.manage',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete'
        ]);

        Role::findByName('bendahara', 'web')->syncPermissions([
            'program.view',
            'proposal.view_approved',
            'files.view',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
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
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete'
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
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete'
        ]);

        Role::findByName('ketua_lingkungan', 'web')->syncPermissions([
            'program.view',
            'files.view',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
        ]);

        Role::findByName('wakil_ketua_lingkungan', 'web')->syncPermissions([
            'program.view',
            'files.view',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
        ]);

        Role::findByName('anggota_komunitas', 'web')->syncPermissions([
            'program.create',
            'program.view',
            'proposal.upload',
            'proposal.delete_rejected',
            'files.view',
            'notulensi.view',
            'documents.view',
            'documents.create',
            'documents.delete',
            'announcements.view',
        ]);

        // ✅ Role Sekretariat (sementara view-only agar aman)
        Role::findByName('sekretariat', 'web')->syncPermissions([
            'program.view',
            'files.view',
            'notulensi.view',
            'documents.view',
            'announcements.view',
        ]);
    }
}