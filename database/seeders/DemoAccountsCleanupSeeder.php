<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoAccountsCleanupSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1) Hapus semua akun @demo.local
            $idsDemoLocal = DB::table('users')
                ->whereNull('deleted_at')
                ->where('email', 'like', '%@demo.local')
                ->pluck('id');

            // 2) Kalau dulu komunitas juga di-seed dan Anda ingin bersih total, tambahkan list email komunitas di sini.
            //    (Kalau tidak mau hapus komunitas, hapus blok ini)
            $komunitasEmails = [
                // isi kalau mau: 'mercurius-0026@gmail.com', dst...
            ];

            $idsKomunitas = collect();
            if (!empty($komunitasEmails)) {
                $idsKomunitas = DB::table('users')
                    ->whereNull('deleted_at')
                    ->whereIn('email', $komunitasEmails)
                    ->pluck('id');
            }

            $ids = $idsDemoLocal->merge($idsKomunitas)->unique()->values();

            if ($ids->isEmpty()) {
                return;
            }

            // bersihin relasi spatie
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('model_id', $ids)
                ->delete();

            DB::table('model_has_permissions')
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('model_id', $ids)
                ->delete();

            // soft delete
            DB::table('users')
                ->whereIn('id', $ids)
                ->update(['deleted_at' => now()]);
        });
    }
}
