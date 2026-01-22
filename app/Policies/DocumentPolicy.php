<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
        if (! $user->can('documents.delete')) return false;

        if ($user->hasRole('super_admin')) return true;

        // yang upload boleh hapus (baik inti maupun bidang)
        if ((int)$document->user_id === (int)$user->id) return true;

        // tim inti boleh hapus dokumen inti (optional, boleh kamu aktifkan)
        $isInti = $user->hasAnyRole(['ketua','wakil_ketua','sekretaris','bendahara']);
        if ($isInti && is_null($document->bidang_id)) return true;

        return false;
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
