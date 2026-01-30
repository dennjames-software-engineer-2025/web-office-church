<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);

        if ($isInti) return true;

        // dokumen bidang: hanya satu bidang
        if (!is_null($document->bidang_id)) {
            return (int)$document->bidang_id === (int)$user->bidang_id;
        }

        // dokumen inti: harus dishare ke bidang user
        $shared = $document->shared_bidang_ids ?? [];
        return $user->bidang_id && in_array((int)$user->bidang_id, array_map('intval', $shared), true);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // wajib punya permission delete
        if (! $user->can('documents.delete')) return false;

        // super admin selalu boleh
        if ($user->hasRole('super_admin')) return true;

        // hanya SEKRETARIS 1/2 di kedudukan DPP INTI yang boleh
        if (! $user->hasRole('sekretaris')) return false;

        $kedudukan = strtolower(trim((string) ($user->kedudukan ?? '')));
        if ($kedudukan !== 'dpp_inti') return false;

        $jabatan = strtolower(trim((string) ($user->jabatan ?? '')));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        return in_array($jabatan, ['sekretaris_1', 'sekretaris_2'], true);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
