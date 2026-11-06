<?php

namespace App\Services\Refersion;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Throwable;

final class RefersionClient
{
    private const int EXPIRY_SKEW_SECONDS = 60;

    public function __construct(
        private readonly HttpFactory $http,
        private readonly CacheRepository $cache,
        private readonly string $tokenUrl,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    public function accessToken(): string
    {
        $cached = $this->cache->get($this->cacheKey());

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        if ($this->clientId === '' || $this->clientSecret === '' || $this->tokenUrl === '') {
            throw new RefersionAuthException('Refersion client credentials are not configured.');
        }

        try {
            $response = $this->http
                ->asForm()
                ->acceptJson()
                ->timeout(15)
                ->post($this->tokenUrl, [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);
        } catch (Throwable) {
            throw new RefersionAuthException('Refersion token request failed.');
        }

        if ($response->failed()) {
            throw new RefersionAuthException(
                'Refersion token request failed with status '.$response->status().'.',
            );
        }

        $token = $this->tokenFrom($response);
        $this->cache->put($this->cacheKey(), $token, $this->ttl($response));

        return $token;
    }

    private function tokenFrom(Response $response): string
    {
        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new RefersionAuthException('Refersion token response is missing access_token.');
        }

        return $token;
    }

    private function ttl(Response $response): int
    {
        $expiresIn = $response->json('expires_in');

        if (! is_int($expiresIn) || $expiresIn < 1) {
            throw new RefersionAuthException('Refersion token response is missing expires_in.');
        }

        return max(1, $expiresIn - self::EXPIRY_SKEW_SECONDS);
    }

    private function cacheKey(): string
    {
        return 'refersion.oauth.token.'.hash('sha256', $this->clientId);
    }
}
