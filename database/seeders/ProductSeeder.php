<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $clothing = Category::where('name', 'Clothing')->first();
        $books = Category::where('name', 'Books')->first();

        // Electronics products
        for ($i = 1; $i <= 5; $i++) {
            $name = "Electronics Product $i";
            Product::create([
                'category_id' => $electronics->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "This is a description for $name",
                'price' => rand(100, 1000),
                'quantity' => rand(10, 100),
                'is_featured' => rand(0, 1),
            ]);
        }

        // Clothing products
        for ($i = 1; $i <= 5; $i++) {
            $name = "Clothing Item $i";
            Product::create([
                'category_id' => $clothing->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "This is a description for $name",
                'price' => rand(20, 200),
                'quantity' => rand(10, 100),
                'is_featured' => rand(0, 1),
            ]);
        }

        // Books products
        for ($i = 1; $i <= 5; $i++) {
            $name = "Book Title $i";
            Product::create([
                'category_id' => $books->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "This is a description for $name",
                'price' => rand(10, 50),
                'quantity' => rand(10, 100),
                'is_featured' => rand(0, 1),
            ]);
        }
    }
}
