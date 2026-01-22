<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ============================================================
         * 1) DATA AKUN DEMO (KAMU CUSTOM DI SINI)
         * ============================================================
         * Catatan:
         * - password: isi kalau mau set / reset password
         * - kalau password null / kosong dan user sudah ada => password tidak berubah
         * - kedudukan akan diisi kalau kolom `kedudukan` ada di tabel users
         */
        $accounts = [
            // ===================== DPP INTI =====================
            [
                'name' => 'Ketua DPP Inti',
                'email' => 'ketua.dppinti@demo.local',
                'password' => 'KetuaDPP123!',
                'role' => 'ketua',
                'kedudukan' => 'dpp_inti',
            ],
            [
                'name' => 'Wakil Ketua DPP Inti',
                'email' => 'wakil.dppinti@demo.local',
                'password' => 'WakilDPP123!',
                'role' => 'wakil_ketua',
                'kedudukan' => 'dpp_inti',
            ],
            [
                'name' => 'Sekretaris 1 DPP Inti',
                'email' => 'sekretaris1.dppinti@demo.local',
                'password' => 'Sekre1DPP123!',
                'role' => 'sekretaris',
                'kedudukan' => 'dpp_inti',
            ],
            [
                'name' => 'Sekretaris 2 DPP Inti',
                'email' => 'sekretaris2.dppinti@demo.local',
                'password' => 'Sekre2DPP123!',
                'role' => 'sekretaris',
                'kedudukan' => 'dpp_inti',
            ],
            [
                'name' => 'Bendahara 1 DPP Inti',
                'email' => 'bendahara1.dppinti@demo.local',
                'password' => 'Benda1DPP123!',
                'role' => 'bendahara',
                'kedudukan' => 'dpp_inti',
            ],
            [
                'name' => 'Bendahara 2 DPP Inti',
                'email' => 'bendahara2.dppinti@demo.local',
                'password' => 'Benda2DPP123!',
                'role' => 'bendahara',
                'kedudukan' => 'dpp_inti',
            ],

            // ===================== BGKP =====================
            [
                'name' => 'Ketua BGKP',
                'email' => 'ketua.bgkp@demo.local',
                'password' => 'KetuaBGKP123!',
                'role' => 'ketua',
                'kedudukan' => 'bgkp',
            ],
            [
                'name' => 'Wakil Ketua BGKP',
                'email' => 'wakil.bgkp@demo.local',
                'password' => 'WakilBGKP123!',
                'role' => 'wakil_ketua',
                'kedudukan' => 'bgkp',
            ],
            [
                'name' => 'Sekretaris 1 BGKP',
                'email' => 'sekretaris1.bgkp@demo.local',
                'password' => 'Sekre1BGKP123!',
                'role' => 'sekretaris',
                'kedudukan' => 'bgkp',
            ],
            [
                'name' => 'Sekretaris 2 BGKP',
                'email' => 'sekretaris2.bgkp@demo.local',
                'password' => 'Sekre2BGKP123!',
                'role' => 'sekretaris',
                'kedudukan' => 'bgkp',
            ],
            [
                'name' => 'Bendahara 1 BGKP',
                'email' => 'bendahara1.bgkp@demo.local',
                'password' => 'Benda1BGKP123!',
                'role' => 'bendahara',
                'kedudukan' => 'bgkp',
            ],
            [
                'name' => 'Bendahara 2 BGKP',
                'email' => 'bendahara2.bgkp@demo.local',
                'password' => 'Benda2BGKP123!',
                'role' => 'bendahara',
                'kedudukan' => 'bgkp',
            ],

            // ===================== LINGKUNGAN =====================
            [
                'name' => 'Ketua Lingkungan',
                'email' => 'ketua.lingkungan@demo.local',
                'password' => 'KetuaLing123!',
                'role' => 'ketua_lingkungan',
                'kedudukan' => 'lingkungan',
            ],
            [
                'name' => 'Wakil Ketua Lingkungan',
                'email' => 'wakil.lingkungan@demo.local',
                'password' => 'WakilLing123!',
                'role' => 'wakil_ketua_lingkungan',
                'kedudukan' => 'lingkungan',
            ],
        ];

        /**
         * ============================================================
         * 2) 32 KOMUNITAS (KAMU CUSTOM NAMA + EMAIL + PASSWORD)
         * ============================================================
         * role akan otomatis dibuat: anggota-komunitas-{slug-nama}
         */
        $komunitas = [
            
            // St Aloysius
            [
                'nama' => 'St Aloysius - Mercurius',
                'email' => 'mercurius-0026@gmail.com',
                'password' => 'mercurius0026',
            ],
            // St Aloysius

            // St Antonius
            [
                'nama' => 'St Antonius - Lucia',
                'email' => 'lucia-0177@gmail.com',
                'password' => 'lucia0177',
            ],
            // St Antonius

            // St Catarina
            [
                'nama' => 'St Catarina - Maria',
                'email' => 'maria-7898@gmail.com',
                'password' => 'maria7898',
            ],
            // St Catarina

            // St Christina
            [
                'nama' => 'St Christina - Andreas',
                'email' => 'andreas-0510@gmail.com',
                'password' => 'andreas0510',
            ],
            // St Christina

            // St Christoporus
            [
                'nama' => 'St Christoporus - Agustinus',
                'email' => 'agustinus-2795@gmail.com',
                'password' => 'agustinus2795',
            ],
            // St Christoporus

            // St Yosef Maria
            [
                'nama' => 'St Yosef Maria - Laurentius',
                'email' => 'laurentius-2137@gmail.com',
                'password' => 'laurentius2137',
            ],
            // St Yosef Maria

            // St Basilius Agung
            [
                'nama' => 'St Basilius Agung - Maria',
                'email' => 'maria-4655@gmail.com',
                'password' => 'maria4655',
            ],
            // St Basilius Agung

            // St Bernadeth
            [
                'nama' => 'St Bernadeth - Agustinus',
                'email' => 'agustinus-4888@gmail.com',
                'password' => 'agustinus-4888',
            ],
            // St Bernadeth

            // St Bonaventura
            [
                'nama' => 'St Bonaventura - Louid',
                'email' => 'louid-8940@gmail.com',
                'password' => 'louid8940',
            ],
            // St Bonaventura

            // St Stella Malutina
            [
                'nama' => 'St Stella Malutina - Maria',
                'email' => 'maria-1127@gmail.com',
                'password' => 'maria1127',
            ],
            // St Stella Malutina

            // St Eduardus
            [
                'nama' => 'St Eduardus - Sulastri',
                'email' => 'sulastri-8880@gmail.com',
                'password' => 'sulastri8880',
            ],
            // St Eduardus

            // St Eleonora
            [
                'nama' => 'St Eleonora - Gabriella',
                'email' => 'gabriella-4558@gmail.com',
                'password' => 'gabriella4558',
            ],
            // St Eleonora

            // St Thomas
            [
                'nama' => 'St Thomas - Anastasia',
                'email' => 'anastasia-8570@gmail.com',
                'password' => 'anastasia8570',
            ],
            // St Thomas

            // St Geovani
            [
                'nama' => 'St Geovani - Ignasius',
                'email' => 'ignasius-4876@gmail.com',
                'password' => 'ignasius4876',
            ],
            // St Geovani

            // St Gregorius
            [
                'nama' => 'St Gregorius - Maria',
                'email' => 'maria-5887@gmail.com',
                'password' => 'maria5887',
            ],
            // St Gregorius

            // St Helena
            [
                'nama' => 'St Helena - Thomas',
                'email' => 'thomas-6878@gmail.com',
                'password' => 'thomas6878',
            ],
            // St Helena

            // St Herman Yosef
            [
                'nama' => 'St Herman Yosef - Suhari',
                'email' => 'suhari-8663@gmail.com',
                'password' => 'suhari8663',
            ],
            // St Herman Yosef

            // St Heronimus
            [
                'nama' => 'St Heronimus - Silvester',
                'email' => 'silvester-0537@gmail.com',
                'password' => 'silvester0537',
            ],
            // St Heronimus

            // St Hubertus
            [
                'nama' => 'St Hubertus - Maria',
                'email' => 'maria-5477@gmail.com',
                'password' => 'maria5477',
            ],
            // St Hubertus

            // Beata Joanna De Aza
            [
                'nama' => 'Beata Joanna De Aza - Nanik',
                'email' => 'nanik-6822@gmail.com',
                'password' => 'nanik6822',
            ],
            // Beata Joanna De Aza

            // St Gerardus
            [
                'nama' => 'St Gerardus - Josephine',
                'email' => 'josephine-1131@gmail.com',
                'password' => 'josephine1131',
            ],
            // St Gerardus

            // St Thomas Aquinas
            [
                'nama' => 'St Thomas Aquinas - Nancy',
                'email' => 'nancy-3218@gmail.com',
                'password' => 'nancy3218',
            ],
            // St Thomas Aquinas

            // St Barnabas
            [
                'nama' => 'St Barnabas - Theodourus',
                'email' => 'theodourus-3868@gmail.com',
                'password' => 'theodourus3868',
            ],
            // St Barnabas

            // St Benediktus
            [
                'nama' => 'St Benediktus - Theresia',
                'email' => 'theresia-5059@gmail.com',
                'password' => 'theresia5059',
            ],
            // St Benediktus

            // St Brigita
            [
                'nama' => 'St Brigita - Elisabeth',
                'email' => 'elisabeth-8088@gmail.com',
                'password' => 'elisabeth8088',
            ],
            // St Brigita

            // St Gregorius Agung
            [
                'nama' => 'St Gregorius Agung - Brigita',
                'email' => 'brigita-6269@gmail.com',
                'password' => 'brigita6269',
            ],
            // St Gregorius Agung

            // St Fidelis
            [
                'nama' => 'St Fidelis - Ellen',
                'email' => 'ellen-2770@gmail.com',
                'password' => 'ellen2770',
            ],
            // St Fidelis

            // St Maria
            [
                'nama' => 'St Maria - Elisabeth',
                'email' => 'elisabeth-6195@gmail.com',
                'password' => 'elisabeth6195',
            ],
            // St Maria

            // St Markus
            [
                'nama' => 'St Markus - Kharis',
                'email' => 'kharis-0182@gmail.com',
                'password' => 'kharis0182',
            ],
            // St Markus

            // St Monica
            [
                'nama' => 'St Monica - Rieska',
                'email' => 'rieska-4450@gmail.com',
                'password' => 'rieska4450',
            ],
            // St Monica

            // St Elizabeth
            [
                'nama' => 'St Elizabeth',
                'email' => 'elyana-9858@gmail.com',
                'password' => 'elyana9858',
            ],
            // St Elizabeth

            // St Fransiskus Xaverius
            [
                'nama' => 'St Fransiskus Xaverius',
                'email' => 'aurelius-0377@gmail.com',
                'password' => 'lucia0177',
            ],
            // St Fransiskus Xaverius
        ];

        /**
         * ============================================================
         * 3) Pastikan role dasar tersedia
         * ============================================================
         */
        $baseRoles = [
            'super_admin',
            'ketua',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'ketua_lingkungan',
            'wakil_ketua_lingkungan',
        ];

        foreach ($baseRoles as $r) {
            Role::findOrCreate($r);
        }

        /**
         * ============================================================
         * 4) Helper: create/update user
         * ============================================================
         */
        $upsertUser = function (array $data) {
            // pastikan role ada
            if (!empty($data['role'])) {
                Role::findOrCreate($data['role']);
            }

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                // CREATE baru (password wajib saat create)
                $payload = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ];

                if (Schema::hasColumn('users', 'kedudukan') && array_key_exists('kedudukan', $data)) {
                    $payload['kedudukan'] = $data['kedudukan'];
                }

                $user = User::create($payload);
            } else {
                // UPDATE data dasar
                $user->name = $data['name'];

                if (Schema::hasColumn('users', 'kedudukan') && array_key_exists('kedudukan', $data)) {
                    $user->kedudukan = $data['kedudukan'];
                }

                // Password hanya diubah kalau kamu isi
                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }

                $user->save();
            }

            if (!empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return $user;
        };

        /**
         * ============================================================
         * 5) Seed akun utama
         * ============================================================
         */
        foreach ($accounts as $a) {
            // validasi minimal biar tidak “setengah”
            if (empty($a['email']) || empty($a['password']) || empty($a['role']) || empty($a['name'])) {
                throw new \Exception("Data akun tidak lengkap. Pastikan name/email/password/role terisi.");
            }
            $upsertUser($a);
        }

        /**
         * ============================================================
         * 6) Seed anggota komunitas
         * ============================================================
         */
        foreach ($komunitas as $k) {
            if (empty($k['nama']) || empty($k['email']) || empty($k['password'])) {
                throw new \Exception("Data komunitas tidak lengkap. Pastikan nama/email/password terisi.");
            }

            $roleName = 'lingkungan-' . Str::slug($k['nama'], '-');
            Role::findOrCreate($roleName);

            $upsertUser([
                'name' => 'Lingkungan - ' . $k['nama'],
                'email' => $k['email'],
                'password' => $k['password'],
                'role' => $roleName,
                'kedudukan' => 'lingkungan',
            ]);
        }
    }
}
