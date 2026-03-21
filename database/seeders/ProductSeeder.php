<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Factory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $factory = Factory::first();
        if (! $factory) {
            $this->command->warn('No factory found. Run HanzoSeeder first.');

            return;
        }

        $categories = Category::where('active', true)->get()->keyBy('slug');
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run HanzoSeeder first.');

            return;
        }

        $products = [
            [
                'title' => 'Tshirt',
                'category_slug' => 'fashion',
                'price_min' => 2.00,
                'price_max' => 4.00,
                'moq' => 50,
                'description' => 'Cotton t-shirts, multiple colors and sizes.',
            ],
            [
                'title' => 'Tshirts',
                'category_slug' => 'fashion',
                'price_min' => null,
                'price_max' => null,
                'moq' => 50,
                'description' => 'Custom printed t-shirts. Contact for pricing.',
            ],
            [
                'title' => 'Polo Shirts',
                'category_slug' => 'fashion',
                'price_min' => 4.50,
                'price_max' => 8.00,
                'moq' => 100,
                'description' => 'Premium cotton polo shirts.',
            ],
            [
                'title' => 'Hoodies',
                'category_slug' => 'fashion',
                'price_min' => 8.00,
                'price_max' => 15.00,
                'moq' => 50,
                'description' => 'Fleece hoodies, various styles.',
            ],
            [
                'title' => 'Corrugated Boxes',
                'category_slug' => 'packaging',
                'price_min' => 0.25,
                'price_max' => 1.50,
                'moq' => 500,
                'description' => 'Custom printed corrugated cartons.',
            ],
            [
                'title' => 'Poly Bags',
                'category_slug' => 'packaging',
                'price_min' => 0.05,
                'price_max' => 0.20,
                'moq' => 10000,
                'description' => 'PE poly bags, various sizes.',
            ],
            [
                'title' => 'LED Flashlights',
                'category_slug' => 'consumer-goods',
                'price_min' => 1.50,
                'price_max' => 5.00,
                'moq' => 200,
                'description' => 'Rechargeable LED flashlights.',
            ],
            [
                'title' => 'Water Bottles',
                'category_slug' => 'consumer-goods',
                'price_min' => null,
                'price_max' => null,
                'moq' => 500,
                'description' => 'Stainless steel water bottles. Request quote for custom branding.',
            ],
            [
                'title' => 'USB Cables',
                'category_slug' => 'electronics',
                'price_min' => 0.30,
                'price_max' => 1.20,
                'moq' => 500,
                'description' => 'Various USB cable types.',
            ],
            [
                'title' => 'Phone Chargers',
                'category_slug' => 'electronics',
                'price_min' => 2.00,
                'price_max' => 6.00,
                'moq' => 100,
                'description' => 'Wall and car chargers.',
            ],
        ];

        foreach ($products as $data) {
            $categorySlug = $data['category_slug'] ?? 'fashion';
            $category = $categories->get($categorySlug) ?? $categories->first();

            Product::firstOrCreate(
                [
                    'factory_id' => $factory->id,
                    'title' => $data['title'],
                    'category_id' => $category->id,
                ],
                [
                    'description' => $data['description'] ?? null,
                    'price_min' => $data['price_min'] ?? null,
                    'price_max' => $data['price_max'] ?? null,
                    'moq' => $data['moq'] ?? 100,
                    'lead_time_days' => 14,
                    'location' => $factory->location_china ?? 'China',
                    'status' => Product::STATUS_LIVE,
                    'images' => [],
                ]
            );
        }
    }
}
