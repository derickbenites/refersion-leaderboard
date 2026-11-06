<?php

namespace Tests\Feature;

use App\Services\Refersion\RefersionClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RefersionAffiliatesTest extends TestCase
{
    private const string BASE_URL = 'https://api.refersion.com/v2026-08';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.refersion.base_url' => self::BASE_URL,
            'services.refersion.token_url' => 'https://auth.refersion.com/oauth/token',
            'services.refersion.client_id' => 'test-client-id',
            'services.refersion.client_secret' => 'test-client-secret',
        ]);
    }

    public function test_it_stops_after_a_short_page(): void
    {
        Http::fake([
            'https://auth.refersion.com/oauth/token' => Http::response([
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'access_token' => 'test-access-token',
            ]),
            self::BASE_URL.'/affiliates*' => function ($request) {
                $page = (int) (self::pageQuery($request->url())['page'] ?? 0);

                $data = match ($page) {
                    1 => [['id' => 11], ['id' => 22]],
                    2 => [['id' => 33]],
                    default => [['id' => 999]],
                };

                return Http::response(['data' => $data]);
            },
        ]);

        $affiliates = $this->app->make(RefersionClient::class)->listAffiliates(2);

        $this->assertSame([11, 22, 33], array_column($affiliates, 'id'));

        $pages = Http::recorded(function ($request): bool {
            return str_contains($request->url(), '/affiliates');
        })->map(fn ($pair) => (int) (self::pageQuery($pair[0]->url())['page'] ?? 0))->values()->all();

        $this->assertSame([1, 2], $pages);
        Http::assertSent(function ($request): bool {
            if (! str_contains($request->url(), '/affiliates')) {
                return false;
            }

            $query = self::pageQuery($request->url());

            return ($query['limit'] ?? null) === '2'
                && $request->hasHeader('Authorization', 'Bearer test-access-token');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private static function pageQuery(string $url): array
    {
        $query = [];
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return $query;
    }
}
