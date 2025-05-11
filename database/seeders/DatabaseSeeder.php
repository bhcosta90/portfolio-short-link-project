<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Bruno Henrique da Costa',
            'email' => 'bhcosta90@gmail.com',
        ]);

        $this->call(ShortLinkSeeder::class);
    }
}
