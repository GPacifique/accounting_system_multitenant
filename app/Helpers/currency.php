<?php

if (! function_exists('currency')) {
    /**
     * Format an amount using the system currency settings.
     *
     * @param  float|int|null  $amount
     * @param  int|null  $decimals  Override decimals (null uses config)
     * @return string
     */
    function currency($amount, $decimals = null)
    {
        $config = config('currency');
        $symbol = $config['symbol'] ?? 'RWF';
        $decimals = is_null($decimals) ? ($config['decimals'] ?? 0) : $decimals;
        $thousand = $config['thousands_separator'] ?? ',';
        $decimalPoint = $config['decimal_point'] ?? '.';

        $amount = $amount ?? 0;

        return $symbol . ' ' . number_format($amount, $decimals, $decimalPoint, $thousand);
    }
}
