<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    private function jabatanKey(User $user): string
    {
        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);
        return $jabatan; // contoh: "bendahara 2" => "bendahara_2"
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'super_admin',
            'ketua',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'ketua_bidang',
            'ketua_sie',
            'anggota_komunitas',
        ]);
    }

    public function view(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) return true;

        if ($user->hasAnyRole(['ketua','wakil_ketua','sekretaris','bendahara'])) {
            return $user->kedudukan !== null
                && $program->target_kedudukan === $user->kedudukan;
        }

        // pembuat program: hanya program di bidangnya (kalau program punya bidang_id)
        if ($user->hasAnyRole(['ketua_bidang','ketua_sie','anggota_komunitas'])) {
            if ($program->bidang_id === null) return true; // kalau nanti program non-bidang
            return $user->bidang_id !== null && $program->bidang_id === $user->bidang_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['ketua_bidang', 'ketua_sie', 'anggota_komunitas']);
    }

    public function update(User $user, Program $program): bool
    {
        return $user->hasAnyRole(['ketua_bidang', 'ketua_sie', 'anggota_komunitas'])
            && $program->status === 'draft'
            && $program->created_by === $user->id;
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->hasAnyRole(['ketua_bidang', 'ketua_sie', 'anggota_komunitas'])
            && $program->status === 'draft'
            && $program->created_by === $user->id
            && ! $program->proposals()->exists();
    }

    public function changeStatus(User $user, Program $program): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // hanya ketua/wakil yang sesuai dengan target kedudukan program
        if (! $user->hasAnyRole(['ketua', 'wakil_ketua'])) return false;

        return $user->kedudukan !== null
            && $program->target_kedudukan === $user->kedudukan;
    }
}
