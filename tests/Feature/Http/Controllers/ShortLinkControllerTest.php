<?php

declare(strict_types = 1);

test('it returns 403 for unauthenticated users', function () {
    $response = $this->get('/api/v1/short-links');
    $response->assertStatus(403);
});

test('', function () {
    loginWithUser();

    $response = $this->get('/api/v1/short-links');
    $response->assertStatus(200);
});
