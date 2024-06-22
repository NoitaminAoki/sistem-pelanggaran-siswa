<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Admin::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@ruangsiswa.com',
            'email_verified_at' => Carbon::now(),
            'is_teacher' => 0,
            'password' => Hash::make('inipasswordadmin123'),
        ]);
    }
}
