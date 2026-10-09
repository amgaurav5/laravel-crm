<?php

use Webkul\ViaSocket\Services\EmbedTokenService;

test('embed token carries the three claims and is signed with HS256', function () {
    $decode = fn (string $segment) => json_decode(base64_decode(strtr($segment, '-_', '+/')), true);

    $token = (new EmbedTokenService('81121', 'proj_test', 's3cr3t-value'))->make('krayin-user-1');

    [$header, $payload, $signature] = explode('.', $token);

    expect($decode($header))->toBe(['alg' => 'HS256', 'typ' => 'JWT']);

    expect($decode($payload))->toBe([
        'org_id' => '81121',
        'project_id' => 'proj_test',
        'unique_identifier' => 'krayin-user-1',
    ]);

    $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', $header.'.'.$payload, 's3cr3t-value', true)), '+/', '-_'), '=');

    expect($signature)->toBe($expected);
});

test('embed token never contains the secret', function () {
    $token = (new EmbedTokenService('81121', 'proj_test', 's3cr3t-value'))->make('krayin-user-1');

    expect($token)->not->toContain('s3cr3t-value');
});
