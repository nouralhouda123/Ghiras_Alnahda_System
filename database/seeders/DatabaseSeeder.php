<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ManagerCampanigSedder::class,
            DepartmentSeeder::class,
            RolePermissionSeeder::class,
           // indicatorsSeeder::class,
        ]);
    }}
