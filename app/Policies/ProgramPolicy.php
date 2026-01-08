<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProgramPolicy
{
    /**
     * Melihat List Program
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['tim_inti', 'tim_bidang', 'super_admin']);
    }

    /**
     * Melihat Detail Program
     */
    public function view(User $user, Program $program): bool
    {
        return $user->hasAnyRole(['tim_inti', 'tim_bidang', 'super_admin']);
    }

    /**
     * Update Program
     * Hanya boleh dilakukan jika status masih 'draft'
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['tim_bidang', 'super_admin']);
    }

    /**
     * Update Program
     * Hanya boleh dilakukan jika status masih 'draft'
     */
    public function update(User $user, Program $program): bool
    {
        return $program->status === 'draft' 
        && $user->hasRole(['tim_bidang', 'super_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Program $program): bool
    {
        return $program->status === 'draft' 
        && $program->proposals()->count() === 0
        && $user->hasRole(['tim_bidang', 'super_admin']);
    }

    /**
     * Mengganti status program
     */
    public function changeStatus(User $user, Program $program): bool
    {
        return $user->hasAnyRole([
            'ketua',
            'bendahara_1',
            'bendahara_2',
            'super_admin',
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Program $program): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Program $program): bool
    {
        return false;
    }
}
