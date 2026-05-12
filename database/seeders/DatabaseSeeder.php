<?php

namespace Database\Seeders;

use App\Enums\StaffApplicationStatus;
use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@cleanswift.test'],
            [
                'name' => 'CleanSwift Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
                'staff_application_status' => null,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'staff@cleanswift.test'],
            [
                'name' => 'Demo Staff',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'phone' => '+15555550100',
                'is_active' => true,
                'staff_application_status' => StaffApplicationStatus::Approved,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'customer@cleanswift.test'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => UserRole::Customer,
                'phone' => '+15555550101',
                'is_active' => true,
                'staff_application_status' => null,
                'email_verified_at' => now(),
            ]
        );

        $services = [
            ['name' => 'Wash', 'slug' => 'wash', 'description' => 'Machine wash, detergent included', 'price' => 4.50, 'unit' => 'kg', 'sort_order' => 1],
            ['name' => 'Dry', 'slug' => 'dry', 'description' => 'Tumble dry and fluff', 'price' => 3.25, 'unit' => 'kg', 'sort_order' => 2],
            ['name' => 'Fold', 'slug' => 'fold', 'description' => 'Neat folding and packaging', 'price' => 2.75, 'unit' => 'kg', 'sort_order' => 3],
            ['name' => 'Iron', 'slug' => 'iron', 'description' => 'Press shirts, trousers, dresses', 'price' => 6.00, 'unit' => 'item', 'sort_order' => 4],
        ];

        foreach ($services as $row) {
            Service::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'unit' => $row['unit'],
                    'is_active' => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
