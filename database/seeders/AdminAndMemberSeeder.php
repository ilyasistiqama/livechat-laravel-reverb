<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;

class AdminAndMemberSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // ADMIN (users table)
        // ======================
        User::insert([
            [
                'name' => 'Admin One',
                'email' => 'admin1@mail.com',
                'password' => Hash::make('password'),
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ======================
        // MEMBER (members table)
        // ======================
        Member::insert([
            [
                'name' => 'Member One',
                'email' => 'member1@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Two',
                'email' => 'member2@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Three',
                'email' => 'member3@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Four',
                'email' => 'member4@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member Five',
                'email' => 'member5@mail.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
