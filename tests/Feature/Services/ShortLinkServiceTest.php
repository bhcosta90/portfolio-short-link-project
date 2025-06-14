<?php

declare(strict_types = 1);

use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;

beforeEach(function (): void {
    $this->shortLinkService = new ShortLinkService();
});

it('creates a short link with the correct attributes', function (): void {
    $data = [
        'endpoint'   => 'https://example.com',
        'is_premium' => false,
    ];

    $shortLinkService = new ShortLinkService();
    $shortLink        = $shortLinkService->store($data);

    expect($shortLink)->toBeInstanceOf(ShortLink::class)
        ->and($shortLink->endpoint)->toBe($data['endpoint'])
        ->and($shortLink->code)->not->toBeNull()
        ->and($shortLink->quantity_days_expired_at)->toBe(3);

    $user = User::factory()->premium()->create();

    $shortLink = $shortLinkService->store($data + ['user_id' => $user->id]);
    expect($shortLink->quantity_days_expired_at)->toBe(7);
});
