<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\BidangSieSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\DemoAccountsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RolePermissionSeeder::class,
            BidangSieSeeder::class,
            SuperAdminSeeder::class,
            DemoAccountsSeeder::class,
        ]);
    }
}
