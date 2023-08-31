<?php

namespace Database\Seeders;

use App\Models\Competitor;
use App\Models\User;
use Carbon\Carbon;
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
            'email_verified_at' => now(),
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
            'password' => Hash::make('admin123'), // password
            'profile' => null,
            'role' => 'admin',
            'remember_token' => Str::random(10),
        ]);
        DB::table('locks')->insert([
            'isLock' => false,
            'votingTime' => Carbon::now()
        ]);
        /* User::factory(200)->create(); */
        /* Competitor::factory(20)->create(); */
    }
}
