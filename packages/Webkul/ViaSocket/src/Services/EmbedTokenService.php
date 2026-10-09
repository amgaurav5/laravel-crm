<?php

namespace Webkul\ViaSocket\Services;

class EmbedTokenService
{
    /**
     * Create a new embed token service instance.
     */
    public function __construct(
        protected string $orgId,
        protected string $projectId,
        protected string $secret,
    ) {}

    /**
     * Sign an HS256 embed token for one end user.
     */
    public function make(string $uniqueIdentifier): string
    {
        $header = $this->encode(['alg' => 'HS256', 'typ' => 'JWT']);

        $payload = $this->encode([
            'org_id' => $this->orgId,
            'project_id' => $this->projectId,
            'unique_identifier' => $uniqueIdentifier,
        ]);

        $signature = $this->base64Url(hash_hmac('sha256', $header.'.'.$payload, $this->secret, true));

        return $header.'.'.$payload.'.'.$signature;
    }

    /**
     * JSON encode and base64url encode a segment.
     */
    protected function encode(array $segment): string
    {
        return $this->base64Url(json_encode($segment, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Base64url encode a string.
     */
    protected function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
