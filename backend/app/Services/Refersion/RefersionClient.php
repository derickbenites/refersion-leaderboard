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
        private readonly string $baseUrl,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function listAffiliates(int $limit = 100): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new RefersionApiException('Refersion affiliate page limit must be from 1 to 100.');
        }

        if ($this->baseUrl === '') {
            throw new RefersionApiException('Refersion base URL is not configured.');
        }

        $affiliates = [];
        $page = 1;

        do {
            $batch = $this->affiliatePage($page, $limit);
            array_push($affiliates, ...$batch);
            $page++;
        } while (count($batch) >= $limit);

        return $affiliates;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function customFields(mixed $affiliateId): array
    {
        $id = $this->pathAffiliateId($affiliateId);

        if ($this->baseUrl === '') {
            throw new RefersionApiException('Refersion base URL is not configured.');
        }

        try {
            $response = $this->http
                ->withToken($this->accessToken())
                ->acceptJson()
                ->timeout(15)
                ->get(rtrim($this->baseUrl, '/').'/affiliates/'.$id.'/custom-fields');
        } catch (RefersionAuthException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new RefersionApiException('Refersion custom field request failed.');
        }

        if ($response->status() === 401) {
            throw new RefersionAuthException('Refersion rejected the access token.');
        }

        if ($response->failed()) {
            throw new RefersionApiException(
                'Refersion custom field request failed with status '.$response->status().'.',
            );
        }

        return $this->resourceList($response, 'Refersion custom field response is missing data.');
    }

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

    /**
     * @return list<array<string, mixed>>
     */
    private function affiliatePage(int $page, int $limit): array
    {
        try {
            $response = $this->http
                ->withToken($this->accessToken())
                ->acceptJson()
                ->timeout(15)
                ->get(rtrim($this->baseUrl, '/').'/affiliates', [
                    'page' => $page,
                    'limit' => $limit,
                ]);
        } catch (RefersionAuthException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new RefersionApiException('Refersion affiliate request failed.');
        }

        if ($response->status() === 401) {
            throw new RefersionAuthException('Refersion rejected the access token.');
        }

        if ($response->failed()) {
            throw new RefersionApiException(
                'Refersion affiliate request failed with status '.$response->status().'.',
            );
        }

        return $this->resourceList($response, 'Refersion affiliate response is missing data.');
    }

    private function pathAffiliateId(mixed $affiliateId): string
    {
        if (is_int($affiliateId) && $affiliateId >= 0) {
            return (string) $affiliateId;
        }

        if (is_float($affiliateId) && floor($affiliateId) === $affiliateId && $affiliateId >= 0 && $affiliateId <= PHP_INT_MAX) {
            return (string) (int) $affiliateId;
        }

        if (is_string($affiliateId) && preg_match('/^(0|[1-9][0-9]*)$/', $affiliateId) === 1) {
            return $affiliateId;
        }

        throw new RefersionApiException('Refersion affiliate id is missing.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function resourceList(Response $response, string $missingMessage): array
    {
        $data = $response->json('data');

        if (! is_array($data) || ! array_is_list($data)) {
            throw new RefersionApiException($missingMessage);
        }

        foreach ($data as $item) {
            if (! is_array($item)) {
                throw new RefersionApiException($missingMessage);
            }
        }

        return $data;
    }

    private function cacheKey(): string
    {
        return 'refersion.oauth.token.'.hash('sha256', $this->clientId);
    }
}
