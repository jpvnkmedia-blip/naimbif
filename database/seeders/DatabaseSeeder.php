<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Hanya memasukkan akaun rasmi pegawai/pentadbir.
     */
    public function run(): void
    {
        // 1. Akaun Pentadbir Sistem
        User::updateOrCreate(
            ['email' => 'admin@jpvnk.gov.my'],
            [
                'name' => 'Pentadbir Sistem JPVNK',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'jajahan' => 'Ibu Pejabat Kota Bharu',
                'jawatan' => 'Pegawai Teknologi Maklumat',
                'no_telefon' => '09-7481234',
            ]
        );

        // 2. Akaun Pegawai Ibu Pejabat (Negeri)
        User::updateOrCreate(
            ['email' => 'negeri@jpvnk.gov.my'],
            [
                'name' => 'Dr. Ahmad Farhan bin Ismail',
                'password' => Hash::make('password'),
                'role' => 'pegawai_negeri',
                'jajahan' => 'Ibu Pejabat Kelantan',
                'jawatan' => 'Ketua Penolong Pengarah (Bahagian Pembangunan Ternakan)',
                'no_telefon' => '09-7485555',
            ]
        );

        // 3. Akaun Pegawai Jajahan Kota Bharu
        User::updateOrCreate(
            ['email' => 'kb@jpvnk.gov.my'],
            [
                'name' => 'En. Mohd Ridzuan bin Abdullah',
                'password' => Hash::make('password'),
                'role' => 'pegawai_jajahan',
                'jajahan' => 'Kota Bharu',
                'jawatan' => 'Pegawai Veterinar Jajahan Kota Bharu',
                'no_telefon' => '019-9876543',
            ]
        );

        // 4. Akaun Pegawai Jajahan Pasir Mas
        User::updateOrCreate(
            ['email' => 'pasirmas@jpvnk.gov.my'],
            [
                'name' => 'Pn. Siti Nurhaliza binti Ramli',
                'password' => Hash::make('password'),
                'role' => 'pegawai_jajahan',
                'jajahan' => 'Pasir Mas',
                'jawatan' => 'Pegawai Veterinar Jajahan Pasir Mas',
                'no_telefon' => '013-9123456',
            ]
        );
    }
}
