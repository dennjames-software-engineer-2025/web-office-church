<?php

namespace App\Policies;

use App\Models\Lpj;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LpjPolicy
{
    private function isSekretaris12(User $user): bool
    {
        if (! $user) return false;
        if (! $user->hasRole('sekretaris')) return false;

        $key = strtolower(trim($user->jabatan_key_active ?? $user->jabatan_key ?? ''));
        if (in_array($key, ['sekretaris_1', 'sekretaris_2'], true)) return true;

        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        return str_starts_with($jabatan, 'sekretaris_1') || str_starts_with($jabatan, 'sekretaris_2');
    }

    private function isKetuaDpp(User $user): bool
    {
        $ked = strtolower(trim((string) $user->kedudukan));
        $ked = str_replace([' ', '-'], '_', $ked);

        // ketua + dpp_inti/hariannya
        return $user->hasRole('ketua') && in_array($ked, ['dpp_inti', 'dpp_harian'], true);
    }

    /**
     * DPP Harian = Ketua Bidang + DPP Inti (ketua/wakil/sekretaris/bendahara)
     * (kamu jelaskan struktur ini).
     */
    private function isDppHarian(User $user): bool
    {
        if ($user->hasRole('ketua_bidang')) return true;

        return $user->kedudukan === 'dpp_inti'
            && $user->hasAnyRole(['ketua','wakil_ketua','sekretaris','bendahara']);
    }

    /**
     * Create LPJ:
     * hanya Ketua Sie pengaju proposal, dan hanya jika proposal approved
     */
    public function create(User $user, Lpj $lpj): bool
    {
        // tidak dipakai kalau create lewat proposal, jadi boleh abaikan / atau implement sesuai kebutuhan.
        return false;
    }

    /**
     * View LPJ:
     * - super_admin: semua
     * - pengaju (ketua sie) pemilik proposal: boleh
     * - Ketua Bidang (bidang sama) + DPP Inti + Bendahara: boleh
     */
    public function view(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // pemilik LPJ (ketua sie yang submit)
        if ((int)$lpj->created_by === (int)$user->id) return true;

        // Ketua Bidang: hanya bidangnya
        // Ketua Bidang:
        // - sebelum disetujui KB: hanya bidangnya sendiri
        // - setelah disetujui KB (masuk romo / forum): semua Ketua Bidang boleh lihat (untuk diskusi)
        if ($user->hasRole('ketua_bidang')) {

            // Inbox awal: hanya Ketua Bidang yang membawahi sie tersebut
            if ($lpj->stage === 'ketua_bidang' && $lpj->status === 'menunggu_ketua_bidang') {
                return $user->bidang_id !== null && (int)$lpj->bidang_id === (int)$user->bidang_id;
            }

            // Forum: setelah approve ketua bidang -> tampil ke semua ketua bidang
            return in_array($lpj->stage, ['romo', 'bendahara'], true)
                || in_array($lpj->status, ['menunggu_romo', 'revisi', 'approved'], true);
        }

        // DPP Inti + Bendahara (bagian dari DPP Harian)
        if ($this->isDppHarian($user)) {
            // kalau sekretaris tapi bukan 1/2, kamu bisa pilih: boleh lihat atau tidak.
            // Di proposal kamu batasi sekretaris non 1/2 -> kosong.
            if ($user->hasRole('sekretaris') && ! $this->isSekretaris12($user)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Download file LPJ:
     * - super_admin: boleh
     * - Bendahara: hanya kalau LPJ sudah approved final
     * - DPP Harian: boleh (opsional kamu mau batasi? kalau mau sama seperti proposal: bebas)
     */
    public function downloadFile(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        if ($user->hasRole('bendahara')) {
            return $lpj->status === 'approved';
        }

        return $this->view($user, $lpj);
    }

    /**
     * Approve Ketua Bidang:
     */
    public function approveKetuaBidang(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return $user->hasRole('ketua_bidang')
            && $user->bidang_id !== null
            && (int)$lpj->bidang_id === (int)$user->bidang_id
            && $lpj->status === 'menunggu_ketua_bidang'
            && $lpj->stage === 'ketua_bidang';
    }

    /**
     * Final Approve/Reject (Ketua DPP atau Sekretaris 1/2).
     */
    public function approveFinal(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        $allowed = $this->isKetuaDpp($user) || $this->isSekretaris12($user);
        return $allowed && $lpj->stage === 'romo' && $lpj->status === 'menunggu_romo';
    }

    public function rejectFinal(User $user, Lpj $lpj): bool
    {
        return $this->approveFinal($user, $lpj);
    }

    /**
     * Notes/Comment:
     * Ketua DPP + Sekretaris 1/2 bisa notes saat tahap romo.
     */
    public function addNotes(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        $allowed = $this->isKetuaDpp($user) || $this->isSekretaris12($user);
        return $allowed && in_array($lpj->stage, ['romo'], true);
    }

    public function delete(User $user, Lpj $lpj): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return (int)$lpj->created_by === (int)$user->id
            && $lpj->status === 'revisi';
    }
}
