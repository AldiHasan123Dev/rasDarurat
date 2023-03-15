<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'SUPER ADMIN'
        ]);
        Role::create([
            'name' => 'COMM'
        ]);
        Role::create([
            'name' => 'FA'
        ]);
    }
}
