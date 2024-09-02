<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Create Role
        Role::query()->create([
            'name' => 'admin'
        ]);

        Role::query()->create([
            'name' => 'coach'
        ]);

        Role::query()->create([
            'name' => 'client'
        ]);

        $users = [
            [
                "email" => "admin@admin.com",
                "password" => Hash::make("admin@admin.com"),
                "registration_type" => "form",
                "profile" => [
                    "branch" => "Tangerang",
                    "name" => "Admin User",
                    "username" => "adminuser",
                    "gender" => "male",
                    "phone" => "1234567890",
                    "address" => "Admin Address",
                    "profile_picture" => null
                ],
                "role" => "admin"
            ],
            [
                "email" => "coach@coach.com",
                "password" => Hash::make("coach@coach.com"),
                "registration_type" => "form",
                "profile" => [
                    "branch" => "Jakarta",
                    "name" => "Coach User",
                    "username" => "coachuser",
                    "gender" => "female",
                    "phone" => "0987654321",
                    "address" => "Coach Address",
                    "profile_picture" => null
                ],
                "role" => "coach"
            ],
            [
                "email" => "client@client.com",
                "password" => Hash::make("client@client.com"),
                "registration_type" => "form",
                "profile" => [
                    "branch" => "Tangerang",
                    "name" => "Client User",
                    "username" => "clientuser",
                    "gender" => "male",
                    "phone" => "1122334455",
                    "address" => "Client Address",
                    "profile_picture" => null
                ],
                "role" => "client"
            ],
        ];

        foreach ($users as $userData) {
            // Insert data ke tabel users
            $user = User::query()->create([
                "email" => $userData['email'],
                "email_verified_at" => Carbon::now(),
                "password" => $userData['password'],
                "registration_type" => $userData['registration_type']
            ]);

            // Dapatkan ID user yang baru dibuat
            $userId = $user->id;

            // Insert data ke tabel user_profiles
            UserProfile::query()->create([
                "user_id" => $userId,
                "branch" => $userData['profile']['branch'],
                "name" => $userData['profile']['name'],
                "username" => $userData['profile']['username'],
                "gender" => $userData['profile']['gender'],
                "phone" => $userData['profile']['phone'],
                "address" => $userData['profile']['address'],
                "profile_picture" => $userData['profile']['profile_picture']
            ]);

            // Dapatkan role berdasarkan nama
            $role = Role::where("name", $userData['role'])->first();

            // Menetapkan role ke pengguna
            if ($user && $role) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
