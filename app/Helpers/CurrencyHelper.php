<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount in RWF (Rwandan Franc) currency
     *
     * @param float|int|string $amount
     * @param int $decimals
     * @param string $prefix
     * @return string
     */
    public static function formatRWF($amount, int $decimals = 0, string $prefix = 'RWF '): string
    {
        if (is_null($amount)) {
            return $prefix . '0';
        }
        
        $numericAmount = is_numeric($amount) ? floatval($amount) : 0;
        
        return $prefix . number_format($numericAmount, $decimals, '.', ',');
    }

    /**
     * Format amount as RWF with thousands separator, no decimals by default
     *
     * @param float|int|string $amount
     * @return string
     */
    public static function rwf($amount): string
    {
        return static::formatRWF($amount, 0);
    }

    /**
     * Format amount as RWF with 2 decimal places
     *
     * @param float|int|string $amount
     * @return string
     */
    public static function rwfWithDecimals($amount): string
    {
        return static::formatRWF($amount, 2);
    }

    /**
     * Get the currency symbol for RWF
     *
     * @return string
     */
    public static function getCurrencySymbol(): string
    {
        return 'RWF';
    }

    /**
     * Get the currency code for Rwanda
     *
     * @return string
     */
    public static function getCurrencyCode(): string
    {
        return 'RWF';
    }

    /**
     * Get the currency name
     *
     * @return string
     */
    public static function getCurrencyName(): string
    {
        return 'Rwandan Franc';
    }

    /**
     * Parse RWF string back to numeric value
     *
     * @param string $rwfString
     * @return float
     */
    public static function parseRWF(string $rwfString): float
    {
        // Remove RWF prefix and any whitespace
        $cleaned = preg_replace('/^RWF\s*/', '', $rwfString);
        
        // Remove thousands separators (commas)
        $cleaned = str_replace(',', '', $cleaned);
        
        // Convert to float
        return floatval($cleaned);
    }
}