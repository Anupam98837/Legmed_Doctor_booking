<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DoctorUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $now = now();

        $doctors = [
            [
                'name' => 'Dr. Arindam Sen',
                'slug' => 'dr-arindam-sen',
                'email' => 'arindam.sen@example.com',
                'phone_number' => '+919800000101',
            ],
            [
                'name' => 'Dr. Meera Sharma',
                'slug' => 'dr-meera-sharma',
                'email' => 'meera.sharma@example.com',
                'phone_number' => '+919800000102',
            ],
            [
                'name' => 'Dr. Rohan Mukherjee',
                'slug' => 'dr-rohan-mukherjee',
                'email' => 'rohan.mukherjee@example.com',
                'phone_number' => '+919800000103',
            ],
        ];

        foreach ($doctors as $doctor) {
            $existing = DB::table('users')->where('email', $doctor['email'])->first();

            DB::table('users')->updateOrInsert(
                ['email' => $doctor['email']],
                [
                    'uuid' => $existing->uuid ?? (string) Str::uuid(),
                    'name' => $doctor['name'],
                    'slug' => $doctor['slug'],
                    'phone_number' => $doctor['phone_number'],
                    'alternative_email' => null,
                    'alternative_phone_number' => null,
                    'whatsapp_number' => null,
                    'email_verified_at' => $existing->email_verified_at ?? $now,
                    'password' => $existing->password ?? Hash::make('password'),
                    'image' => null,
                    'address' => null,
                    'role' => 'doctor',
                    'role_short_form' => 'DOC',
                    'status' => 'active',
                    'last_login_at' => $existing->last_login_at ?? null,
                    'last_login_ip' => $existing->last_login_ip ?? null,
                    'remember_token' => $existing->remember_token ?? Str::random(60),
                    'created_by' => $existing->created_by ?? null,
                    'created_at_ip' => $existing->created_at_ip ?? null,
                    'created_at' => $existing->created_at ?? $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                    'metadata' => json_encode([
                        'seed' => 'DoctorUserSeeder',
                        'note' => 'Sample doctor user',
                    ]),
                ]
            );
        }
    }
}
