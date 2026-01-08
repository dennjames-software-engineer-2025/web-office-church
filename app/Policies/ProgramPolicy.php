<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['tim_inti', 'tim_bidang', 'super_admin']);
    }

    public function view(User $user, Program $program): bool
    {
        // super_admin & tim_inti boleh lihat semua
        if ($user->hasAnyRole(['super_admin', 'tim_inti'])) {
            return true;
        }

        // tim_bidang hanya boleh lihat program dalam bidang yang sama
        return $user->hasRole('tim_bidang')
            && $user->bidang_id !== null
            && $program->bidang_id === $user->bidang_id;
    }

    public function create(User $user): bool
    {
        // sesuai requirement: hanya tim_bidang yang membuat program
        return $user->hasRole('tim_bidang');
    }

    public function update(User $user, Program $program): bool
    {
        // Rule opsi A: hanya creator boleh edit, hanya saat draft
        return $user->hasRole('tim_bidang')
            && $program->status === 'draft'
            && $program->created_by === $user->id;
    }

    public function delete(User $user, Program $program): bool
    {
        // hanya creator, hanya draft, dan hanya jika belum ada proposal
        return $user->hasRole('tim_bidang')
            && $program->status === 'draft'
            && $program->created_by === $user->id
            && $program->proposals()->count() === 0;
    }

    public function changeStatus(User $user, Program $program): bool
    {
        // super_admin full authority
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // hanya ketua (jabatan) dari tim_inti yang boleh ubah status program
        return $user->hasRole('tim_inti') && $user->jabatan === 'ketua';
    }
}
