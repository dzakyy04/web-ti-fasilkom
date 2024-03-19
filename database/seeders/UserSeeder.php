<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin Web TI 1',
            'username' => 'adminwebti1',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Admin Web TI 2',
            'username' => 'adminwebti2',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
