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
            ['slug' => 'fashion', 'name' => 'Fashion', 'description' => 'Fashion and textiles', 'moq_default' => 500, 'price_min_per_unit' => 2.50, 'price_max_per_unit' => 25.00],
            ['slug' => 'packaging', 'name' => 'Packaging', 'description' => 'Packaging and branding', 'moq_default' => 1000, 'price_min_per_unit' => 0.15, 'price_max_per_unit' => 3.00],
            ['slug' => 'consumer-goods', 'name' => 'Consumer Goods', 'description' => 'Consumer products', 'moq_default' => 200, 'price_min_per_unit' => 1.00, 'price_max_per_unit' => 15.00],
            ['slug' => 'machinery', 'name' => 'Machinery', 'description' => 'Machinery and equipment', 'moq_default' => 10, 'price_min_per_unit' => 150.00, 'price_max_per_unit' => 5000.00],
            ['slug' => 'electronics', 'name' => 'Electronics', 'description' => 'Electronic components', 'moq_default' => 100, 'price_min_per_unit' => 5.00, 'price_max_per_unit' => 100.00],
        ];
        foreach ($categories as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
