<?php

namespace App\Helpers;

class InputSanitizer
{
    /**
     * Sanitize string input - remove HTML tags and trim
     */
    public static function sanitizeString(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        return trim(strip_tags($input));
    }

    /**
     * Sanitize HTML input - allow only safe tags
     */
    public static function sanitizeHtml(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        $allowedTags = '<p><br><strong><em><u><a><ul><ol><li>';
        return strip_tags($input, $allowedTags);
    }

    /**
     * Sanitize numeric input
     */
    public static function sanitizeNumeric($input): ?int
    {
        if ($input === null || $input === '') {
            return null;
        }

        $sanitized = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        return $sanitized !== false && $sanitized !== '' ? (int) $sanitized : null;
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        return filter_var($input, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize phone number - keep only digits
     */
    public static function sanitizePhone(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        return preg_replace('/[^0-9]/', '', $input);
    }

    /**
     * Sanitize username - alphanumeric and underscores only
     */
    public static function sanitizeUsername(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }
        
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }
}