<?php

declare(strict_types = 1);

use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;

beforeEach(function () {
    $this->shortLinkService = new ShortLinkService();
});

it('creates a short link with the correct attributes', function () {
    $data = [
        'endpoint'   => 'https://example.com',
        'is_premium' => false,
    ];

    $shortLinkService = new ShortLinkService();
    $shortLink        = $shortLinkService->store(new User(), $data);

    expect($shortLink)->toBeInstanceOf(ShortLink::class)
        ->and($shortLink->endpoint)->toBe($data['endpoint'])
        ->and($shortLink->code)->not->toBeNull()
        ->and($shortLink->quantity_days_expired_at)->toBe(3);

    $shortLink = $shortLinkService->store(new User(['is_premium' => true]), $data);
    expect($shortLink->quantity_days_expired_at)->toBe(7);
});

it('generates a unique code', function () {
    ShortLink::factory()->create([
        'code'       => 'ABC123',
        'expired_at' => now()->addDays(1),
    ]);

    $shortLinkService = new ShortLinkService();

    $reflection = new ReflectionClass(ShortLinkService::class);
    $method     = $reflection->getMethod('generateNewCode');
    $method->setAccessible(true);

    // Invoca o método privado
    $code = $method->invoke($shortLinkService);

    // Verifica se o código gerado é único e no formato esperado
    expect($code)->not->toBe('ABC123')
        ->and($code)->toMatch('/^[A-Z0-9]{6,}$/');
});
