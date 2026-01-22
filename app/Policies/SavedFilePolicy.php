<?php

namespace App\Policies;

use App\Models\SavedFile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SavedFilePolicy
{
    /**
     * Hanya yang punya files.manage boleh manage folder+item.
     */
    public function manage(User $user): bool
    {
        return $user->can('files.manage');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Siapa yang boleh lihat item di Manajemen File (untuk yang files.view).
     */
    public function view(User $user, SavedFile $item): bool
    {
        // private = tidak boleh dilihat orang lain
        if ($item->is_private) return false;

        // super admin boleh
        if ($user->hasRole('super_admin')) return true;

        // kalau dishare ke roles
        $sharedRoles = $item->shared_roles ?? [];
        if (!empty($sharedRoles) && $user->hasAnyRole($sharedRoles)) {
            return true;
        }

        // kalau dishare ke bidang
        $sharedBidangIds = $item->shared_bidang_ids ?? [];
        if ($user->bidang_id && in_array((int)$user->bidang_id, array_map('intval', $sharedBidangIds), true)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SavedFile $savedFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SavedFile $savedFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SavedFile $savedFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SavedFile $savedFile): bool
    {
        return false;
    }
}
