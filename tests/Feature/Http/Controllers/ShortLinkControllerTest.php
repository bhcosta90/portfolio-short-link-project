<?php

declare(strict_types = 1);

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('it returns 403 for unauthenticated users', function (): void {
    $response = $this->getJson('/api/v1/short-links');
    $response->assertStatus(403);
});

test('it returns 200 for authenticated users', function (): void {
    loginWithUser();

    $response = $this->getJson('/api/v1/short-links');
    $response->assertStatus(200);
});

test('it creates a short link and returns the correct structure', function (): void {
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

test('it validates the request when creating a short link', function (): void {
    $this->postJson('/api/v1/short-links', [
        'endpoint' => fake()->url(),
        'slug'     => fake()->slug(),
    ])->assertStatus(422);
});

test('it creates a short link for premium users', function (): void {
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

test('it retrieves a short link by its hash ID', function (): void {
    $shortLink = ShortLink::factory()->create()->refresh();

    $this->getJson('/api/v1/short-links/' . $shortLink->hash_id)->assertJsonStructure([
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

it('redirects to the correct endpoint for a given slug with cache', function () {
    $shortLink = ShortLink::factory()->create(['slug' => 'test-slug']);
    $response  = $this->get('/s/test-slug');
    $response->assertRedirect($shortLink->endpoint);
});

it('redirects to the correct endpoint for a given slug', function () {
    $shortLink = ShortLink::factory()->create(['slug' => 'test-slug']);

    Cache::shouldReceive('remember')
        ->once()
        ->with('slug_test-slug', 60 * 24, Closure::class)
        ->andReturn($shortLink->endpoint);

    $response = $this->get('/s/test-slug');

    $response->assertRedirect($shortLink->endpoint);
});

it('returns a message in local environment for a given slug', function () {
    $this->app['env'] = 'local';

    $shortLink = ShortLink::factory()->create(['slug' => 'test-slug']);

    Cache::shouldReceive('remember')
        ->once()
        ->with('slug_test-slug', 60 * 24, Closure::class)
        ->andReturn($shortLink->endpoint);

    $response = $this->get('/s/test-slug');

    $response->assertSee('Vai ser redirecionado para o endpoint: ' . $shortLink->endpoint);
});

it('redirects to the correct endpoint for a given key with cache', function () {
    $shortLink = ShortLink::factory()->create()->refresh();
    $response  = $this->get('/r/' . $shortLink->code);
    $response->assertRedirect($shortLink->endpoint);
});

it('redirects to the correct endpoint for a given key', function () {
    $shortLink = ShortLink::factory()->create()->refresh();

    Cache::shouldReceive('remember')
        ->once()
        ->with('id_' . $shortLink->id, 60 * 24, Closure::class)
        ->andReturn($shortLink->endpoint);

    $response = $this->get('/r/' . $shortLink->id);

    $response->assertRedirect($shortLink->endpoint);
});

it('returns a message in local environment for a given key', function () {
    $this->app['env'] = 'local';

    $shortLink = ShortLink::factory()->create();

    Cache::shouldReceive('remember')
        ->once()
        ->with('id_' . $shortLink->id, 60 * 24, Closure::class)
        ->andReturn($shortLink->endpoint);

    $response = $this->get('/r/' . $shortLink->id);

    $response->assertSee('Vai ser redirecionado para o endpoint: ' . $shortLink->endpoint);
});
