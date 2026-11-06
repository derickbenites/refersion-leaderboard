<?php

namespace App\Services\Leaderboard;

final class Ranking
{
    public static function commission(array $affiliate): int|float|null
    {
        $performance = $affiliate['performance'] ?? null;

        if (! is_array($performance)) {
            return null;
        }

        $total = $performance['total'] ?? null;

        if (! is_array($total)) {
            return null;
        }

        $commission = $total['commission'] ?? null;

        return is_int($commission) || is_float($commission) ? $commission : null;
    }

    /**
     * @param  list<array{id: int, name: string, commission: int|float}>  $affiliates
     * @return list<array{rank: int, id: int, name: string, commission: int|float}>
     */
    public static function topFive(array $affiliates): array
    {
        usort($affiliates, function (array $left, array $right): int {
            $byCommission = $right['commission'] <=> $left['commission'];

            return $byCommission !== 0 ? $byCommission : $left['id'] <=> $right['id'];
        });

        $ranked = [];

        foreach (array_slice($affiliates, 0, 5) as $index => $affiliate) {
            $ranked[] = [
                'rank' => $index + 1,
                'id' => $affiliate['id'],
                'name' => $affiliate['name'],
                'commission' => $affiliate['commission'],
            ];
        }

        return $ranked;
    }
}
