<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'admin' => Role::query()->create(['name' => 'admin']),
                'coach' => Role::query()->create(['name' => 'coach']),
                'client' => Role::query()->create(['name' => 'client']),
            ];

            // Data User
            $users = [
                [
                    "name" => "Admin User",
                    "email" => "admin@admin.com",
                    "password" => Hash::make("admin@admin.com"),
                    "registration_type" => "form",
                    "profile" => [
//                        "branch" => "tangerang",
                        "username" => "adminuser",
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
//                        "branch" => "jakarta",
                        "username" => "coachjustin",
                        "gender" => "male",
                        "phone" => "0987654321",
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
                    "profile" => [
//                        "branch" => "tangerang",
                        "username" => "adamlevin",
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
                        //                        "branch" => "jakarta",
                        "username" => "helga",
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
                    "profile" => [
                        //                        "branch" => "tangerang",
                        "username" => "sumikojp",
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
                    "profile" => [
                        //                        "branch" => "tangerang",
                        "username" => "mastatang",
                        "gender" => "male",
                        "phone" => "1122334455",
                        "address" => "Bandung",
                        "profile_picture" => null
                    ],
                    "role" => 'client'
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
                    "registration_type" => $userData['registration_type']
                ]);

                // Buat profile user
                UserProfile::query()->create([
                    "user_id" => $user->id,
//                    "branch" => $userData['profile']['branch'],
                    "username" => $userData['profile']['username'],
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

            // Buat Rooms
            $rooms = [
                [
                    'name' => 'Room Alpha'
                ],
                [
                    'name' => 'Room Beta'
                ]
            ];

            foreach ($rooms as $room) {
                Room::query()->create($room);
            }

            // Buat Lesson Types
            $lessonTypes = [
                [
                    'name' => 'Reformer',
                    'quota' => 5
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
                    'time_slot_id' => 1, // ID time_slot yang sesuai
                    'lesson_id' => 1,    // ID lesson yang sesuai
                    'lesson_type_id' => 1, // ID lesson_type yang sesuai
                    'user_id' => 2,      // ID user (coach)
                    'room_id' => 1,      // ID room yang sesuai
                    'quota' => 6, // Quota
                    'status' => 'Available' // Status
                ],
                [
                    'date' => $startDate->copy(),
                    'time_slot_id' => 1,
                    'lesson_id' => 2,
                    'lesson_type_id' => 2,
                    'user_id' => 4,
                    'room_id' => 2,
                    'quota' => 3,
                    'status' => 'Available',
                ],
                [
                    'date' => $startDate->copy(),
                    'time_slot_id' => 2,
                    'lesson_id' => 4,
                    'lesson_type_id' => 1,
                    'user_id' => 4,
                    'room_id' => 1,
                    'quota' => 5,
                    'status' => 'Available',
                ],
                [
                    'date' => $startDate->copy()->addDays(1),
                    'time_slot_id' => 1,
                    'lesson_id' => 4,
                    'lesson_type_id' => 1,
                    'user_id' => 2,
                    'room_id' => 1,
                    'quota' => 5,
                    'status' => 'Available',
                ],
                [
                    'date' => $startDate->copy()->addDays(1),
                    'time_slot_id' => 3,
                    'lesson_id' => 2,
                    'lesson_type_id' => 2,
                    'user_id' => 4,
                    'room_id' => 2,
                    'quota' => 3,
                    'status' => 'Available',
                ]
                // Tambahkan lebih banyak jadwal sesuai kebutuhan
            ];

            foreach ($scheduleData as $schedule) {
                LessonSchedule::query()->create($schedule);
            }

            // Certification
            // Seeder Coach Certifications
            $certifications = [
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
            }
        });
    }
}
