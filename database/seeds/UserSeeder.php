<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'position' => null,
            'position_order' => 999,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Receptionist
        DB::table('users')->insert([
            'name' => 'Receptionist User',
            'email' => 'receptionist@example.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'position' => null,
            'position_order' => 999,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 1 - Pimpinan
        DB::table('users')->insert([
            'name' => 'Dr. Budi Santoso, S.H., M.H.',
            'email' => 'budi@example.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Pimpinan',
            'position_order' => 1,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 2 - Hakim Tinggi
        DB::table('users')->insert([
            'name' => 'Siti Nurhaliza, S.H., M.H.',
            'email' => 'siti@example.com',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Hakim Tinggi',
            'position_order' => 2,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 3 - Panitera
        DB::table('users')->insert([
            'name' => 'Ahmad Dahlan, S.H.',
            'email' => 'ahmad@example.com',
            'phone' => '081234567894',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Panitera',
            'position_order' => 3,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 4 - Sekretaris
        DB::table('users')->insert([
            'name' => 'Dewi Sartika, S.H.',
            'email' => 'dewi@example.com',
            'phone' => '081234567895',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Sekretaris',
            'position_order' => 3,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 5 - Panitera Muda
        DB::table('users')->insert([
            'name' => 'Eko Prasetyo, S.H.',
            'email' => 'eko@example.com',
            'phone' => '081234567896',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Panitera Muda',
            'position_order' => 4,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 6 - Kepala Bagian
        DB::table('users')->insert([
            'name' => 'Fitri Handayani, S.E.',
            'email' => 'fitri@example.com',
            'phone' => '081234567897',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Kepala Bagian',
            'position_order' => 5,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 7 - Panitera Pengganti
        DB::table('users')->insert([
            'name' => 'Gilang Ramadhan, S.H.',
            'email' => 'gilang@example.com',
            'phone' => '081234567898',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Panitera Pengganti',
            'position_order' => 6,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Employee 8 - Kepala Sub Bagian
        DB::table('users')->insert([
            'name' => 'Hendra Wijaya, S.Kom.',
            'email' => 'hendra@example.com',
            'phone' => '081234567899',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Kepala Sub Bagian',
            'position_order' => 7,
            'presence_status' => 'ada',
            'presence_updated_at' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}