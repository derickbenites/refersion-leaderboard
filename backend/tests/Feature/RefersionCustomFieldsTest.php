<?php

namespace Tests\Feature;

use App\Services\Refersion\RefersionApiException;
use App\Services\Refersion\RefersionClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RefersionCustomFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.refersion.base_url' => 'https://api.refersion.com/v2026-08',
            'services.refersion.token_url' => 'https://auth.refersion.com/oauth/token',
            'services.refersion.client_id' => 'test-client-id',
            'services.refersion.client_secret' => 'test-client-secret',
        ]);
    }

    public function test_it_reads_custom_fields_for_a_numeric_affiliate_id(): void
    {
        Http::fake([
            'https://auth.refersion.com/oauth/token' => Http::response([
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'access_token' => 'test-access-token',
            ]),
            'https://api.refersion.com/v2026-08/affiliates/15/custom-fields' => Http::response([
                'data' => [[
                    'id' => 9,
                    'label' => 'Display in leaderboard',
                    'value' => 'TRUE',
                ]],
            ]),
        ]);

        $fields = $this->app->make(RefersionClient::class)->customFields(15.0);

        $this->assertSame('TRUE', $fields[0]['value']);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.refersion.com/v2026-08/affiliates/15/custom-fields'
                && $request->hasHeader('Authorization', 'Bearer test-access-token');
        });
    }

    public function test_it_rejects_an_affiliate_id_that_is_not_a_number(): void
    {
        Http::fake();

        try {
            $this->app->make(RefersionClient::class)->customFields('15abc');
            $this->fail('Expected RefersionApiException.');
        } catch (RefersionApiException) {
            Http::assertNothingSent();
        }
    }
}
