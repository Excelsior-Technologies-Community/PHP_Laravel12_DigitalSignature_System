<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Authsign;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Authsign::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        if (!$admin->wasRecentlyCreated && !$admin->is_admin) {
            $admin->is_admin = true;
            $admin->save();
        }
    }
}
