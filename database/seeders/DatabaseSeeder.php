<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\ProductStore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $store = new ProductStore();
        foreach ([
            ['Wireless Mouse', 25, 19.99],
            ['USB-C Hub', 8, 49.50],
            ['Desk Lamp', 12, 34.00],
        ] as [$name, $quantity, $price]) {
            $store->create(['name' => $name, 'quantity' => $quantity, 'price' => $price]);
        }
    }
}
