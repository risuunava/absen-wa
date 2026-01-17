<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\School;
use App\Models\AttendanceTimeSetting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks untuk MySQL
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Create default school if not exists
        if (!School::first()) {
            School::create([
                'name' => 'Sekolah Anda',
                'latitude' => -6.8632300,
                'longitude' => 108.0491849,
                'radius' => 100
            ]);
            
            $this->command->info('Sekolah berhasil dibuat.');
        } else {
            $this->command->info('Sekolah sudah ada, skip...');
        }

        // Clear existing attendance time settings dengan delete biasa
        AttendanceTimeSetting::query()->delete();
        
        // Reset auto increment
        DB::statement('ALTER TABLE attendance_time_settings AUTO_INCREMENT = 1');
        
        // Create multiple attendance time settings
        $timeSettings = [
            [
                'name' => 'Absen Masuk Pagi',
                'start_time' => '06:30',
                'end_time' => '07:30',
                'type' => 'masuk',
                'days_of_week' => [1, 2, 3, 4, 5], // JANGAN encode sebagai JSON string
                'description' => 'Waktu absen masuk pagi',
                'is_active' => true
            ],
            [
                'name' => 'Absen Masuk Siang',
                'start_time' => '12:30',
                'end_time' => '13:30',
                'type' => 'masuk',
                'days_of_week' => [1, 2, 3, 4, 5],
                'description' => 'Waktu absen masuk siang',
                'is_active' => true
            ],
            [
                'name' => 'Absen Pulang Pagi',
                'start_time' => '11:30',
                'end_time' => '12:30',
                'type' => 'pulang',
                'days_of_week' => [1, 2, 3, 4, 5],
                'description' => 'Waktu absen pulang pagi',
                'is_active' => true
            ],
            [
                'name' => 'Absen Pulang Siang',
                'start_time' => '15:30',
                'end_time' => '16:30',
                'type' => 'pulang',
                'days_of_week' => [1, 2, 3, 4, 5],
                'description' => 'Waktu absen pulang siang',
                'is_active' => true
            ],
            [
                'name' => 'Absen Sabtu Pagi',
                'start_time' => '07:00',
                'end_time' => '08:00',
                'type' => 'masuk',
                'days_of_week' => [6],
                'description' => 'Waktu absen khusus hari Sabtu',
                'is_active' => true
            ]
        ];

        foreach ($timeSettings as $setting) {
            // Laravel akan otomatis meng-encode array ke JSON karena ada cast di model
            AttendanceTimeSetting::create($setting);
        }
        
        $this->command->info('Waktu absen berhasil dibuat.');

        // Clear users table untuk contoh data
        User::query()->delete();
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        // Create admin user
        User::create([
            'username' => 'admin',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'full_name' => 'Administrator Sistem'
        ]);
        
        $this->command->info('Admin user berhasil dibuat.');

        // Create sample teacher
        User::create([
            'username' => 'guru1',
            'phone' => '081234567891',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'full_name' => 'Budi Santoso',
            'subject' => 'Matematika'
        ]);
        
        $this->command->info('Guru user berhasil dibuat.');

        // Create sample students
        $students = [
            [
                'username' => 'murid1',
                'phone' => '081234567892',
                'password' => Hash::make('murid123'),
                'role' => 'murid',
                'full_name' => 'Siti Nurhaliza',
                'class' => 'XII IPA 1'
            ],
            [
                'username' => 'murid2',
                'phone' => '081234567893',
                'password' => Hash::make('murid123'),
                'role' => 'murid',
                'full_name' => 'Ahmad Fauzi',
                'class' => 'XII IPA 2'
            ]
        ];

        foreach ($students as $student) {
            User::create($student);
            $this->command->info("User {$student['username']} berhasil dibuat.");
        }

        // Enable foreign key checks kembali
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('========================================');
        $this->command->info('Waktu absen yang dibuat:');
        $this->command->info('- 06:30-07:30 (Masuk Pagi) - Senin-Jumat');
        $this->command->info('- 12:30-13:30 (Masuk Siang) - Senin-Jumat');
        $this->command->info('- 11:30-12:30 (Pulang Pagi) - Senin-Jumat');
        $this->command->info('- 15:30-16:30 (Pulang Siang) - Senin-Jumat');
        $this->command->info('- 07:00-08:00 (Sabtu Pagi) - Sabtu saja');
        $this->command->info('========================================');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: username=admin, password=admin123');
        $this->command->info('Guru: username=guru1, password=guru123');
        $this->command->info('Murid: username=murid1, password=murid123');
        $this->command->info('========================================');
        $this->command->info('Test URL: /debug-time untuk cek waktu server');
    }
}