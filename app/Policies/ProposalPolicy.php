<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Sekretaris yang dianggap "Sekretaris 1/2"
     */
    private function isSekretaris12(User $user): bool
    {
        if (! $user) return false;
        if (! $user->hasRole('sekretaris')) return false;

        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        return in_array($jabatan, ['sekretaris_1', 'sekretaris_2'], true);
    }

    /**
     * Romo = Ketua DPP Inti (kedudukan dpp_inti + role ketua)
     */
    private function isRomo(User $user): bool
    {
        return $user->hasRole('ketua') && $user->kedudukan === 'dpp_inti';
    }

    /**
     * Anggota DPP Inti (yang ikut DPP Harian dari sisi DPP inti)
     */
    private function isDppIntiMember(User $user): bool
    {
        return $user->kedudukan === 'dpp_inti'
            && $user->hasAnyRole(['ketua','wakil_ketua','sekretaris','bendahara']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ketua_sie') || $user->hasRole('super_admin');
    }

    public function view(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // pengaju selalu boleh lihat
        if ($proposal->created_by === $user->id) return true;

        // ketua bidang: lintas bidang setelah dpp_harian
        if ($user->hasRole('ketua_bidang')) {
            if (in_array($proposal->stage, ['dpp_harian', 'romo', 'bendahara'], true) || $proposal->status === 'approved') {
                return true;
            }
            return $user->bidang_id && $proposal->bidang_id === $user->bidang_id;
        }

        // DPP inti boleh lihat setelah masuk DPP Harian ke atas
        if ($this->isDppIntiMember($user)) {
            return in_array($proposal->stage, ['dpp_harian', 'romo', 'bendahara'], true)
                || in_array($proposal->status, ['menunggu_romo','approved'], true);
        }

        // bendahara hanya setelah approved
        if ($user->hasRole('bendahara')) {
            return $proposal->status === 'approved' || $proposal->stage === 'bendahara';
        }

        return false;
    }

    public function approveKetuaBidang(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return $user->hasRole('ketua_bidang')
            && $user->bidang_id !== null
            && $proposal->bidang_id === $user->bidang_id
            && $proposal->status === 'menunggu_ketua_bidang'
            && $proposal->stage === 'ketua_bidang';
    }

    public function setDppDeadline(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // sesuai requirement: Romo + Sekretaris 1/2
        return $this->isSekretaris12($user) || $this->isRomo($user);
    }

    /**
     * NOTES hanya Sekretaris 1/2 (sementara).
     */
    public function addNotes(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        $isSek12 = $this->isSekretaris12($user);
        $stageOk  = in_array($proposal->stage, ['dpp_harian', 'romo'], true);
        $statusOk = in_array($proposal->status, ['dpp_harian', 'menunggu_romo'], true);

        return $isSek12 && $stageOk && $statusOk;
    }

    public function approveRomo(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return $this->isRomo($user)
            && $proposal->stage === 'romo'
            && $proposal->status === 'menunggu_romo';
    }

    public function rejectRomo(User $user, Proposal $proposal): bool
    {
        return $this->approveRomo($user, $proposal);
    }

    /**
     * ✅ DELETE (versi requirement terbaru):
     * - Romo / Sekretaris 1/2 / super_admin: boleh hapus kapan saja
     * - Ketua Sie (pengaju): boleh hapus hanya saat status = "revisi"
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // Romo / Sekretaris 1/2 boleh kapan saja
        if ($this->isRomo($user) || $this->isSekretaris12($user)) {
            return true;
        }

        // Ketua Sie (pengaju) boleh hapus jika revisi
        return $proposal->created_by === $user->id
            && $proposal->status === 'revisi';
    }

    public function viewFile(User $user, Proposal $proposal): bool
    {
        return $this->view($user, $proposal);
    }

    public function downloadFile(User $user, Proposal $proposal): bool
    {
        return $this->view($user, $proposal);
    }

    public function endDppHarian(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        $allowed = $this->isSekretaris12($user) || $this->isRomo($user);

        return $allowed && $proposal->stage === 'dpp_harian' && $proposal->status === 'dpp_harian';
    }
}
