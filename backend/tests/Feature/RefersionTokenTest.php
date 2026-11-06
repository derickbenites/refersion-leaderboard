<?php

namespace Tests\Feature;

use App\Services\Refersion\RefersionAuthException;
use App\Services\Refersion\RefersionClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RefersionTokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.refersion.token_url' => 'https://auth.refersion.com/oauth/token',
            'services.refersion.client_id' => 'test-client-id',
            'services.refersion.client_secret' => 'test-client-secret',
        ]);
    }

    public function test_it_exchanges_client_credentials_and_reuses_the_token(): void
    {
        Http::fake([
            'https://auth.refersion.com/oauth/token' => Http::response([
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'access_token' => 'test-access-token',
            ]),
        ]);

        $client = $this->app->make(RefersionClient::class);

        $this->assertSame('test-access-token', $client->accessToken());
        $this->assertSame('test-access-token', $client->accessToken());

        Http::assertSentCount(1);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://auth.refersion.com/oauth/token'
                && $request['client_id'] === 'test-client-id'
                && $request['client_secret'] === 'test-client-secret';
        });
    }

    public function test_it_refuses_to_request_a_token_without_credentials(): void
    {
        config([
            'services.refersion.client_id' => '',
            'services.refersion.client_secret' => '',
        ]);

        Http::fake();

        $client = $this->app->make(RefersionClient::class);

        try {
            $client->accessToken();
            $this->fail('Expected RefersionAuthException.');
        } catch (RefersionAuthException) {
            Http::assertNothingSent();
        }
    }
}
