<?php

namespace Database\Seeders;

use App\Models\Competitor;
use App\Models\Lock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->insert([
            'voter_id' => 'admin',
            'email_verified_at' => now(),
            'slug' => 'admin123',
            'password' => Hash::make('admin123'), // password
            'role' => 'admin',
            'remember_token' => Str::random(10),
        ]);
        Lock::factory(1)->create();
        // User::factory(200)->create();
        // Competitor::factory(20)->create();
    }
}
