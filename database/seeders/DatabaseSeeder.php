<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Helpers\LessonCodeHelper;
use App\Models\Booking;
use App\Models\CoachCertification;
use App\Models\Lesson;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Gunakan transaksi database untuk memastikan integritas data
        DB::transaction(function () {
            // Buat Role
            $roles = [
                'super_admin' => Role::query()->create(['name' => 'super_admin']),
                'admin' => Role::query()->create(['name' => 'admin']),
                'coach' => Role::query()->create(['name' => 'coach']),
                'client' => Role::query()->create(['name' => 'client']),
            ];

            // Data User
            $users = [
                [
                    "name" => "Super Admin",
                    "email" => "super.admin@admin.com",
                    "password" => Hash::make("super.admin@admin.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1234567890",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'super_admin'
                ],
                [
                    "name" => "Admin",
                    "email" => "admin@admin.com",
                    "password" => Hash::make("admin@admin.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1234567890",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'admin'
                ],
                [
                    "name" => "Justin",
                    "email" => "justin@coach.com",
                    "password" => Hash::make("justin@coach.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "male",
                        "phone" => "081234567890",
                        "address" => "Jakarta",
                        "profile_picture" => null
                    ],
                    "role" => 'coach'
                ],
                [
                    "name" => "Adam Levin",
                    "email" => "adam.levin@client.com",
                    "password" => Hash::make("adam.levin@client.com"),
                    "registration_type" => "form",
                    "credit_balance" => 10,
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1122334455",
                        "address" => "California",
                        "profile_picture" => null
                    ],
                    "role" => 'client'
                ],
                [
                    "name" => "Helga",
                    "email" => "helga@coach.com",
                    "password" => Hash::make("helga@coach.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "female",
                        "phone" => "0987654321",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'coach'
                ],
                [
                    "name" => "Sumiko",
                    "email" => "sumiko@client.com",
                    "password" => Hash::make("sumiko@client.com"),
                    "registration_type" => "form",
                    "credit_balance" => 10,
                    "profile" => [
                        "gender" => "female",
                        "phone" => "1122334455",
                        "address" => "Yokohama",
                        "profile_picture" => null
                    ],
                    "role" => 'client'
                ],
                [
                    "name" => "Tatang",
                    "email" => "mastatang@client.com",
                    "password" => Hash::make("mastatang@client.com"),
                    "registration_type" => "form",
                    "credit_balance" => 10,
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1122334455",
                        "address" => "Bandung",
                        "profile_picture" => null
                    ],
                    "role" => 'client'
                ],
                [
                    "name" => "MFS",
                    "email" => "support@ptmfs.co.id",
                    "password" => Hash::make("support@ptmfs.co.id"),
                    "registration_type" => "form",                    
                    "profile" => [
                        "gender" => "male",
                        "phone" => "01234567890",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'super_admin'
                ],
                [
                    "name" => "Super Admin Ohana",
                    "email" => "ohana.super@admin.com",
                    "password" => Hash::make("ohana.super@admin.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1234567890",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'super_admin'
                ],
                [
                    "name" => "Admin Ohana",
                    "email" => "admin.ohana@admin.com",
                    "password" => Hash::make("admin.ohana@admin.com"),
                    "registration_type" => "form",
                    "profile" => [
                        "gender" => "male",
                        "phone" => "1234567890",
                        "address" => "Tangerang",
                        "profile_picture" => null
                    ],
                    "role" => 'admin'
                ],
            ];

            // Loop data user dan buat user beserta profile serta assign role
            foreach ($users as $userData) {
                // Buat user
                $user = User::query()->create([
                    "name" => $userData['name'],
                    "email" => $userData['email'],
                    "email_verified_at" => Carbon::now(),
                    "password" => $userData['password'],
                    "registration_type" => $userData['registration_type'],
                    "credit_balance" => $userData['credit_balance'] ?? 0
                ]);

                // Buat profile user
                UserProfile::query()->create([
                    "user_id" => $user->id,
                    "gender" => $userData['profile']['gender'],
                    "phone" => $userData['profile']['phone'],
                    "address" => $userData['profile']['address'],
                    "profile_picture" => $userData['profile']['profile_picture']
                ]);

                // Assign role ke user (tanpa query ulang role)
                $user->roles()->attach($roles[$userData['role']]->id);
            }

            // Buat Time Slots
            $start = strtotime('09:00');
            $end = strtotime('18:00');

            while ($start < $end) {
                $end_time = $start + 3600; // Tambah 1 jam

                DB::table('time_slots')->insert([
                    'start_time' => date('H:i', $start),
                    'end_time' => date('H:i', $end_time),
                    'duration' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $start = $end_time; // Waktu berikutnya
            }
        
            // Buat Lesson Types
            $lessonTypes = [
                [
                    'name' => 'Group',
                    'quota' => 8
                ],
                [
                    'name' => 'Private',
                    'quota' => 3
                ]
            ];

            foreach ($lessonTypes as $lessonType) {
                LessonType::query()->create($lessonType);
            }

            // Buat Lesson
            $lessons = [
                [
                    'name' => 'All Level'
                ],
                [
                    'name' => 'Bootcamp'
                ],
                [
                    'name' => 'Booty & Core'
                ],
                [
                    'name' => 'Abs & Back'
                ]
            ];

            foreach ($lessons as $lesson) {
                Lesson::query()->create($lesson);
            }

            // Buat Schedule
            $startDate = Carbon::now();
            $scheduleData = [
                [
                    'date' => $startDate->copy(),
                    'lesson_code' => "OHANA-202411-000001",
                    'time_slot_id' => 1, // ID time_slot yang sesuai (1 - 9)
                    'lesson_id' => 1,    // ID lesson yang sesuai (1 - 4)
                    'lesson_type_id' => 1, // ID lesson_type yang sesuai (1 - 2)
                    'user_id' => 3,      // ID user (coach) 3 & 5
                    'quota' => 5,
                    'credit_price' => 5
                ],
                [
                    'date' => $startDate->copy()->addDay(),
                    'lesson_code' => "OHANA-202411-000002",
                    'time_slot_id' => 1, // ID time_slot yang sesuai (1 - 9)
                    'lesson_id' => 4,    // ID lesson yang sesuai (1 - 4)
                    'lesson_type_id' => 1, // ID lesson_type yang sesuai (1 - 2)
                    'user_id' => 5,      // ID user (coach) 3 & 5
                    'quota' => 3,
                    'credit_price' => 3
                ],
                [
                    'date' => $startDate->copy()->subDay(3),
                    'lesson_code' => "OHANA-202411-000003",
                    'time_slot_id' => 1, // ID time_slot yang sesuai (1 - 9)
                    'lesson_id' => 2,    // ID lesson yang sesuai (1 - 4)
                    'lesson_type_id' => 1, // ID lesson_type yang sesuai (1 - 2)
                    'user_id' => 5,      // ID user (coach) 3 & 5
                    'quota' => 3,
                    'credit_price' => 3
                ],
                [
                    'date' => $startDate->copy()->addDays(2),
                    'lesson_code' => "OHANA-202411-000004",
                    'time_slot_id' => 4, // ID time_slot yang sesuai (1 - 9)
                    'lesson_id' => 3,    // ID lesson yang sesuai (1 - 4)
                    'lesson_type_id' => 2, // ID lesson_type yang sesuai (1 - 2)
                    'user_id' => 3,      // ID user (coach) 3 & 5
                    'quota' => 2,
                    'credit_price' => 5
                ]
            ];

            foreach ($scheduleData as $schedule) {
                LessonSchedule::query()->create($schedule);
            }

            // Certification
            // Seeder Coach Certifications
            /* $certifications = [
                'Bachelor of Physiotherapy',
                'STOTT Intensive Mat Pilates (IMP)',
                'STOTT Intensive Reformer (IR)',
                'STOTT Intensive Chair Cadilac & Barrel (ICCB)',
                'Optimization Lumbo-Pelvic Region',
                'Total Barre Amplified',
                'Pre Natal Pilates Reformer',
                'Pre Natal Pilates Cadillac'
            ];

            // Tambahkan 3-5 sertifikasi random untuk coach
            for ($i = 0; $i < rand(3, 5); $i++) {
                CoachCertification::query()->create([
                    'user_id' => 2,
                    'certification_name' => $certifications[array_rand($certifications)],
                    'date_received' => now()->subYears(rand(1, 5)),
                    'issuing_organization' => 'Japan Conditioning Academy'
                ]);
            }

            for ($i = 0; $i < rand(3, 5); $i++) {
                CoachCertification::query()->create([
                    'user_id' => 4,
                    'certification_name' => $certifications[array_rand($certifications)],
                    'date_received' => now()->subYears(rand(1, 5)),
                    'issuing_organization' => 'Pilates Institute'
                ]);
            } */
        });
    }
}
