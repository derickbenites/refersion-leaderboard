<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LeaderboardTest extends TestCase
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

    public function test_it_returns_the_top_five_eligible_affiliates(): void
    {
        $values = [
            1 => ' TRUE',
            2 => 'TRUE',
            3 => ' TRUE',
            4 => ' TRUE',
            5 => ' TRUE',
            6 => ' TRUE',
            7 => ' TRUE',
            8 => ' TRUE',
        ];
        $commissions = [
            1 => 10,
            2 => 50,
            3 => 40,
            4 => 30,
            5 => 20,
            6 => 15,
            7 => 5,
            8 => 100,
        ];

        Http::preventStrayRequests();
        Http::fake([
            'https://auth.refersion.com/oauth/token' => Http::response([
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'access_token' => 'test-access-token',
            ]),
            self::BASE_URL.'/affiliates/*/custom-fields' => function ($request) use ($values) {
                preg_match('#/affiliates/(\d+)/custom-fields$#', $request->url(), $matches);
                $id = (int) ($matches[1] ?? 0);

                return Http::response([
                    'data' => [[
                        'id' => 1,
                        'label' => $id === 4 ? 'Show publicly' : 'Display in leaderboard',
                        'value' => $values[$id] ?? 'TRUE',
                    ]],
                ]);
            },
            self::BASE_URL.'/affiliates*' => Http::response([
                'data' => array_map(fn (int $id): array => [
                    'id' => $id,
                    'firstName' => 'Affiliate',
                    'lastName' => (string) $id,
                    'performance' => ['total' => ['commission' => $commissions[$id]]],
                ], array_keys($commissions)),
            ]),
        ]);

        $this->getJson('/api/leaderboard')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    ['rank' => 1, 'id' => 8, 'name' => 'Affiliate 8', 'commission' => 100],
                    ['rank' => 2, 'id' => 3, 'name' => 'Affiliate 3', 'commission' => 40],
                    ['rank' => 3, 'id' => 5, 'name' => 'Affiliate 5', 'commission' => 20],
                    ['rank' => 4, 'id' => 6, 'name' => 'Affiliate 6', 'commission' => 15],
                    ['rank' => 5, 'id' => 1, 'name' => 'Affiliate 1', 'commission' => 10],
                ],
            ]);
    }

    public function test_it_hides_upstream_auth_failures(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://auth.refersion.com/oauth/token' => Http::response([
                'error' => 'invalid_client',
            ], 401),
        ]);

        $response = $this->getJson('/api/leaderboard');

        $response->assertStatus(502)->assertExactJson([
            'message' => 'Refersion authentication failed.',
        ]);
        $this->assertStringNotContainsString('test-client-secret', $response->getContent());
    }
}
