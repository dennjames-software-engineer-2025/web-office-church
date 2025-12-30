<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Sie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BidangSieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================================
        // Bidang Formatio
        // ==================================

        $formatio = Bidang::firstOrCreate(['nama_bidang' => 'Formatio']);

        foreach (['BIAK', 'REKAT', 'OMK', 'Lansia'] as $sie) {
            Sie::firstOrCreate([
                'nama_sie' => $sie,
                'bidang_id' => $formatio->id,
            ]);
        }

        // End Formatio


        // ==================================
        // Bidang Sumber
        // ==================================

        $sumber = Bidang::firstOrCreate(['nama_bidang' => 'Sumber']);

        foreach (['Liturgi', 'Katakese', 'Kerasulan KS'] as $sie) {
            Sie::firstOrCreate([
                'nama_sie' => $sie,
                'bidang_id' => $sumber->id,
            ]);
        }

        // End Sumber


        // ==================================
        // Bidang Kerasulan Khusus
        // ==================================

        $kerasulan_khusus = Bidang::firstOrCreate(['nama_bidang' => 'Kerasulan Khusus']);

        foreach (['Komsos', 'Pendidikan', 'Animasi Panggilan'] as $sie) {
            Sie::firstOrCreate([
                'nama_sie' => $sie,
                'bidang_id' => $kerasulan_khusus->id,
            ]);
        }

        // End Kerasulan Khusus


        // ==================================
        // Bidang Kerasulan Umum
        // ==================================

        $kerasulan_umum = Bidang::firstOrCreate(['nama_bidang' => 'Kerasulan Umum']);

        foreach (['PSE', 'PHUBB'] as $sie) {
            Sie::firstOrCreate([
                'nama_sie' => $sie,
                'bidang_id' => $kerasulan_umum->id,
            ]);
        }
    }
}
