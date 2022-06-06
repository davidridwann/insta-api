<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::insert([
            'name' => 'David',
            'username' => 'davidridwann',
            'email' => 'david@gmail.com',
            'password' => Hash::make('123123123'),
        ]);
    }
}
