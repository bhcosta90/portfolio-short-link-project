<?php

declare(strict_types = 1);

use App\Models\ShortLink;
use App\Models\User;

test('it returns 403 for unauthenticated users', function () {
    $response = $this->getJson('/api/v1/short-links');
    $response->assertStatus(403);
});

test('it returns 200 for authenticated users', function () {
    loginWithUser();

    $response = $this->getJson('/api/v1/short-links');
    $response->assertStatus(200);
});

test('it creates a short link and returns the correct structure', function () {
    $this->postJson('/api/v1/short-links', [
        'endpoint' => fake()->url(),
    ])->assertJsonStructure([
        'data' => [
            'id',
            'endpoint',
            'slug',
            'short_link',
            'user_id',
            'created_at',
            'updated_at',
        ],
    ]);
});

test('it validates the request when creating a short link', function () {
    $this->postJson('/api/v1/short-links', [
        'endpoint' => fake()->url(),
        'slug'     => fake()->slug(),
    ])->assertStatus(422);
});

test('it creates a short link for premium users', function () {
    loginWithUser(User::factory()->create([
        'is_premium' => true,
    ]));

    $this->postJson('/api/v1/short-links', [
        'endpoint' => fake()->url(),
        'slug'     => fake()->slug(),
    ])->assertJsonStructure([
        'data' => [
            'id',
            'endpoint',
            'slug',
            'short_link',
            'user_id',
            'created_at',
            'updated_at',
        ],
    ]);
});

test('it retrieves a short link by its hash ID', function () {
    $shortLink = ShortLink::factory()->create()->refresh();

    $this->getJson('/api/v1/short-links/' . $shortLink->hash_id)->dump()->assertJsonStructure([
        'data' => [
            'id',
            'endpoint',
            'slug',
            'short_link',
            'user_id',
            'created_at',
            'updated_at',
        ],
    ]);
});
