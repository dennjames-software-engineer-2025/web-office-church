<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dihubungkan ke Models User 
        // firstOrCreate berguna untuk memeriksa
        $super = User::firstOrCreate(
            ['email' => 'superadmin@office.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'team_type' => 'Inti',
                'jabatan'   => 'super_admin',
                'bidang_id' => null,
                'sie_id'    => null,
            ]
        );

        $super->assignRole('super_admin');
    }
}
