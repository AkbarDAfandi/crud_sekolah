<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'name'=> "admin",
            'email'=> "admin@admin.com",
            'password'=> Hash::make('admin#123'),
            'role' => 'Admin'
        ]);

        DB::table('classes')->insert([
            'name' => "kelas tambahan",
            'teacher_id' => '1'
        ]);

        DB::table('subjects')->insert([
            'name' => "Math"
        ]);

        DB::table('students')->insert([
            'nipd' => "12123",
            'name' => "Ucok",
            'class_id' => 1,
            'gender' => 'L',
            'date_of_birth' => "2012-12-12"
        ]);

        DB::table('subject_teacher')->insert([
            'subject_id' => 1,
            'teacher_id' => 1
        ]);

    }
}
