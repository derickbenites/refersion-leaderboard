<?php

namespace Tests\Unit;

use App\Services\Leaderboard\Ranking;
use PHPUnit\Framework\TestCase;

class RankingTest extends TestCase
{
    public function test_it_reads_total_commission_and_keeps_the_top_five(): void
    {
        $this->assertSame(40.5, Ranking::commission([
            'performance' => ['total' => ['commission' => 40.5]],
        ]));
        $this->assertNull(Ranking::commission([
            'performance' => ['total' => ['commission' => '40']],
        ]));

        $ranked = Ranking::topFive([
            ['id' => 2, 'name' => 'Bea', 'commission' => 10],
            ['id' => 8, 'name' => 'Hana', 'commission' => 100],
            ['id' => 3, 'name' => 'Cio', 'commission' => 40],
            ['id' => 5, 'name' => 'Eli', 'commission' => 20],
            ['id' => 6, 'name' => 'Fay', 'commission' => 15],
            ['id' => 1, 'name' => 'Ada', 'commission' => 10],
            ['id' => 7, 'name' => 'Gus', 'commission' => 5],
        ]);

        $this->assertSame([8, 3, 5, 6, 1], array_column($ranked, 'id'));
        $this->assertSame([1, 2, 3, 4, 5], array_column($ranked, 'rank'));
    }
}
