<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default school dengan koordinat Anda
        School::create([
            'name' => 'Sekolah Anda',
            'latitude' => -6.8632300, // Perhatikan titik desimal, bukan koma
            'longitude' => 108.0491849, // Perhatikan titik desimal, bukan koma
            'radius' => 100
        ]);

        // Create admin user
        User::create([
            'username' => 'admin',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'full_name' => 'Administrator Sistem'
        ]);

        // Create sample teacher
        User::create([
            'username' => 'guru1',
            'phone' => '081234567891',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'full_name' => 'Budi Santoso',
            'subject' => 'Matematika'
        ]);

        // Create sample students
        User::create([
            'username' => 'murid1',
            'phone' => '081234567892',
            'password' => Hash::make('murid123'),
            'role' => 'murid',
            'full_name' => 'Siti Nurhaliza',
            'class' => 'XII IPA 1'
        ]);

        User::create([
            'username' => 'murid2',
            'phone' => '081234567893',
            'password' => Hash::make('murid123'),
            'role' => 'murid',
            'full_name' => 'Ahmad Fauzi',
            'class' => 'XII IPA 2'
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Koordinat sekolah: Latitude: -6.8632300, Longitude: 108.0491849');
        $this->command->info('Admin: username=admin, phone=081234567890, password=admin123');
        $this->command->info('Guru: username=guru1, phone=081234567891, password=guru123');
        $this->command->info('Murid: username=murid1, phone=081234567892, password=murid123');
    }
}