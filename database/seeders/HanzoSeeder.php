<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Factory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HanzoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hanzo.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@hanzo.com'],
            [
                'name' => 'Sample Buyer',
                'company_name' => 'Buyer Co',
                'country' => 'USA',
                'city' => 'New York',
                'password' => Hash::make('password'),
                'status' => 'pending',
                'email_verified_at' => now(),
            ]
        );
        if (! $buyer->hasRole('buyer')) {
            $buyer->assignRole('buyer');
        }

        $factoryUser = User::firstOrCreate(
            ['email' => 'factory@hanzo.com'],
            [
                'name' => 'Sample Factory',
                'password' => Hash::make('password'),
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );
        if (! $factoryUser->hasRole('factory')) {
            $factoryUser->assignRole('factory');
        }
        Factory::firstOrCreate(
            ['user_id' => $factoryUser->id],
            [
                'factory_name' => 'Sample China Factory',
                'location_china' => 'Guangdong',
                'verification_status' => 'approved',
            ]
        );
        $factoryUser->update(['status' => 'approved']);

        $categories = [
            ['slug' => 'electronics', 'name' => 'Electronics', 'description' => 'Electronic components and devices', 'moq_default' => 100],
            ['slug' => 'textiles', 'name' => 'Textiles', 'description' => 'Fabrics and garments', 'moq_default' => 500],
            ['slug' => 'hardware', 'name' => 'Hardware', 'description' => 'Metal and hardware products', 'moq_default' => 200],
        ];
        foreach ($categories as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
