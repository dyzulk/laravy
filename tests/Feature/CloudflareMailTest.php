<?php

uses(Tests\TestCase::class);

use Illuminate\Support\Facades\Config;

test('cloudflare mailer configuration is present', function () {
    expect(config('mail.mailers.cloudflare.transport'))->toBe('cloudflare');
});

test('cloudflare services configuration is present', function () {
    expect(config('services.cloudflare.account_id'))->not->toBeNull();
    expect(config('services.cloudflare.key'))->not->toBeNull();
});
