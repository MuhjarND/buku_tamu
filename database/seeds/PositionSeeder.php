<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            [
                'name' => 'Pimpinan',
                'order' => 1,
                'show_in_public' => true,
                'description' => 'Pimpinan tertinggi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hakim Tinggi',
                'order' => 2,
                'show_in_public' => true,
                'description' => 'Hakim Tinggi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Panitera',
                'order' => 3,
                'show_in_public' => true,
                'description' => 'Panitera',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sekretaris',
                'order' => 3,
                'show_in_public' => true,
                'description' => 'Sekretaris',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Panitera Muda',
                'order' => 4,
                'show_in_public' => true,
                'description' => 'Panitera Muda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Bagian',
                'order' => 5,
                'show_in_public' => true,
                'description' => 'Kepala Bagian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Panitera Pengganti',
                'order' => 6,
                'show_in_public' => true,
                'description' => 'Panitera Pengganti',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Sub Bagian',
                'order' => 7,
                'show_in_public' => true,
                'description' => 'Kepala Sub Bagian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff',
                'order' => 8,
                'show_in_public' => false, // Tidak tampil di public
                'description' => 'Staff Umum',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('positions')->insert($positions);
    }
}