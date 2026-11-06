<?php

namespace App\Services\Leaderboard;

final class Eligibility
{
    public const string LABEL = 'Display in leaderboard';

    public const string VALUE = ' TRUE';

    /**
     * @param  list<array<string, mixed>>  $customFields
     */
    public static function allows(array $customFields): bool
    {
        foreach ($customFields as $field) {
            if (! is_array($field)) {
                continue;
            }

            if (($field['label'] ?? null) === self::LABEL && ($field['value'] ?? null) === self::VALUE) {
                return true;
            }
        }

        return false;
    }
}
