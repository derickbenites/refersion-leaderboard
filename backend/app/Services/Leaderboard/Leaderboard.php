<?php

namespace App\Services\Leaderboard;

use App\Services\Refersion\RefersionApiException;
use App\Services\Refersion\RefersionClient;

final class Leaderboard
{
    public function __construct(private readonly RefersionClient $client) {}

    /**
     * @return list<array{rank: int, id: int, name: string, commission: int|float}>
     */
    public function topFive(): array
    {
        $eligible = [];

        foreach ($this->client->listAffiliates() as $affiliate) {
            $id = $this->id($affiliate['id'] ?? null);
            $fields = $this->client->customFields($id);

            if (! Eligibility::allows($fields)) {
                continue;
            }

            $commission = Ranking::commission($affiliate);

            if ($commission === null) {
                continue;
            }

            $eligible[] = [
                'id' => $id,
                'name' => $this->name($affiliate),
                'commission' => $commission,
            ];
        }

        return Ranking::topFive($eligible);
    }

    private function id(mixed $affiliateId): int
    {
        if (is_int($affiliateId) && $affiliateId >= 0) {
            return $affiliateId;
        }

        if (is_string($affiliateId) && preg_match('/^(0|[1-9][0-9]*)$/', $affiliateId) === 1) {
            return (int) $affiliateId;
        }

        throw new RefersionApiException('Refersion affiliate id is missing.');
    }

    private function name(array $affiliate): string
    {
        $first = is_string($affiliate['firstName'] ?? null) ? $affiliate['firstName'] : '';
        $last = is_string($affiliate['lastName'] ?? null) ? $affiliate['lastName'] : '';

        return trim($first.' '.$last);
    }
}
