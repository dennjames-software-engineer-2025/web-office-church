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

        // Paling aman: gunakan jabatan_key_active / jabatan_key (kalau tersedia)
        $key = strtolower(trim($user->jabatan_key_active ?? $user->jabatan_key ?? ''));

        if (in_array($key, ['sekretaris_1', 'sekretaris_2'], true)) {
            return true;
        }

        // Fallback: normalize dari jabatan string biasa
        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        // Supaya "sekretaris_2_dpp_harian" tetap lolos
        return str_starts_with($jabatan, 'sekretaris_1') || str_starts_with($jabatan, 'sekretaris_2');
    }

    /**
     * Romo = Ketua DPP Inti (kedudukan dpp_inti + role ketua)
     */
    private function isRomo(User $user): bool
    {
        $ked = strtolower(trim((string) $user->kedudukan));
        $ked = str_replace([' ', '-'], '_', $ked);

        return $user->hasRole('ketua') && in_array($ked, ['dpp_inti', 'dpp_harian'], true);
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
        /* Ketua Bidang hanya bisa lihat Proposal bidangnya */
        if ($user->hasRole('ketua_bidang')) {
            // Supaya semua Ketua Bidang bisa melihat semua Proposal yang sudah Approved
            if ($proposal->status === 'approved') return true;

            // supaya Ketua Bidang bisa men-tracking Proposal | Berlaku  untuk semua Status
            return $user->bidang_id !== null && (int)$proposal->bidang_id === (int)$user->bidang_id;
        }

        // DPP inti boleh lihat setelah masuk DPP Harian ke atas
        if ($this->isDppIntiMember($user) || $this->isRomo($user)) {
            return in_array($proposal->stage, ['dpp_harian', 'romo', 'bendahara'], true)
                || in_array($proposal->status, ['menunggu_romo', 'revisi', 'approved'], true);
        }

        // bendahara hanya setelah approved
        if ($user->hasRole('bendahara')) {
            return in_array($proposal->status, ['menunggu_romo', 'approved'], true);
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
        if ($user->hasRole(['super_admin', 'ketua'])) return true;

        $isSek12 = $this->isSekretaris12($user);
        $stageOk  = in_array($proposal->stage, ['dpp_harian', 'romo'], true);
        $statusOk = in_array($proposal->status, ['dpp_harian', 'menunggu_romo'], true);

        return $isSek12 && $stageOk && $statusOk;
    }

    public function approveRomo(User $user, Proposal $proposal): bool
    {
        if (! $this->canFinalApproveOrReject($user)) return false;

        // wajib sesuai brief: hanya saat menunggu romo
        return $proposal->stage === 'romo' && $proposal->status === 'menunggu_romo';
    }

    public function rejectRomo(User $user, Proposal $proposal): bool
    {
        if (! $this->canFinalApproveOrReject($user)) return false;

        // wajib sesuai brief: hanya saat menunggu romo
        return $proposal->stage === 'romo' && $proposal->status === 'menunggu_romo';
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
        if ($user->hasRole('super_admin')) return true;

        if ($user->hasRole('bendahara')) {
            return $proposal->status === 'approved';
        }

        return $this->view($user, $proposal);
    }

    // Lihat BPP
    public function viewReceipt(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // BPP ada ketika Proposal di Approved
        if ($proposal->status !== 'approved') return false;
        if (empty($proposal->receipt_path)) return false;

        // Ketua Sie Pengaju
        if ((int)$proposal->created_by === (int)$user->id) return true;

        // Ketua Bidang yang Approve proposal (tidak lintas Bidang)
        if ($user->hasRole('ketua_bidang')) {
            return (int)$proposal->bidang_id === (int)$user->bidang_id
            && (int)$proposal->ketua_bidang_approved_by === (int)$user->id;
        }

        return false;
    }
    // End Lihat BPP

    // Unduh BPP
    public function downloadReceipt(User $user, Proposal $proposal): bool 
    {
        return $this->viewReceipt($user, $proposal);
    }
    // End Unduh BPP

    public function endDppHarian(User $user, Proposal $proposal): bool
    {
        if ($user->hasRole('super_admin')) return true;

        $allowed = $this->isSekretaris12($user) || $this->isRomo($user);

        return $allowed && $proposal->stage === 'dpp_harian' && $proposal->status === 'dpp_harian';
    }

    /**
     * Aktor final = Ketua DPP (Romo) ATAU Sekretaris 1/2
     */
    private function canFinalApproveOrReject(User $user): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return $this->isRomo($user) || $this->isSekretaris12($user);
    }
}
