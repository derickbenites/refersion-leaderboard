<?php

namespace Tests\Unit;

use App\Services\Leaderboard\Eligibility;
use PHPUnit\Framework\TestCase;

class EligibilityTest extends TestCase
{
    public function test_it_accepts_only_the_exact_leading_space_true_value(): void
    {
        $this->assertTrue(Eligibility::allows([
            ['label' => 'Website', 'value' => 'https://example.com'],
            ['label' => 'Display in leaderboard', 'value' => ' TRUE'],
        ]));
    }

    public function test_it_rejects_near_misses(): void
    {
        $nearMisses = ['TRUE', 'true', ' True', ' true', ' TRUE ', 'TRUE '];

        foreach ($nearMisses as $value) {
            $this->assertFalse(Eligibility::allows([
                ['label' => 'Display in leaderboard', 'value' => $value],
            ]), 'Accepted near miss: '.json_encode($value));
        }

        $this->assertFalse(Eligibility::allows([
            ['label' => 'display in leaderboard', 'value' => ' TRUE'],
        ]));
        $this->assertFalse(Eligibility::allows([]));
    }
}
