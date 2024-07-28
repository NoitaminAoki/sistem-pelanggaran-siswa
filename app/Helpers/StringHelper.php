<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * Escape special characters for a LIKE query.
     *
     * @param string $value
     * @param string $char
     *
     * @return string
     */
    public static function escapeLike(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char . $char, $char . '%', $char . '_'],
            $value
        );
    }

    public static function formatGender($gender, $reverse = false)
    {
        if ($reverse) {
            return self::formatGenderReverse($gender);
        }
        $normalized = strtolower(str_replace(' ', '', $gender));

        if ($normalized === 'laki-laki') {
            return 'L';
        } elseif ($normalized === 'perempuan') {
            return 'P';
        }

        return null; // or throw an exception if invalid gender is passed
    }

    private static function formatGenderReverse($gender)
    {
        if ($gender === 'L') {
            return 'Laki-laki';
        } elseif ($gender === 'P') {
            return 'Perempuan';
        }

        return null; // or throw an exception if invalid gender is passed
    }
}
