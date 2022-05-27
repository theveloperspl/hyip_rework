<?php

namespace App\Classes;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Exception;

class CurrenciesConverter
{
    /**
     * Convert and format cents to dollars
     *
     * @param int $cents
     * @return float
     */
    public function toDollars(int $cents): float
    {
        return number_format(bcdiv($cents, 100, 2), 2, '.', '');
    }

    /**
     * Convert dollars to cents
     *
     * @param float $dollars
     * @return int
     */
    public function toCents(float $dollars): int
    {
        return bcmul($dollars, 100);
    }

    /**
     * Convert satoshi to Bitcoin
     *
     * @param int $satoshi
     * @return string
     */
    public function toBitcoin(int $satoshi): string
    {
        $satoshi = bcdiv($satoshi, 100000000, 8);
        $satoshi = sprintf("%.8f", $satoshi);
        $satoshi = rtrim($satoshi, 0);
        return rtrim($satoshi, '.');
        // or short one line: return rtrim(rtrim(sprintf("%.8f", bcdiv($satoshi, 100000000, 8)), 0), '.');
    }

    /**
     * Convert Bitcoin to satoshi
     *
     * @param string $crypto
     * @return string
     */
    public function toSatoshi(string $crypto): string
    {
        return sprintf("%d", bcmul($crypto, 100000000));
    }

    /**
     * Convert USD to satoshi
     *
     * @param int $amount
     * @param string $cryptoLongCode
     * @param string $currency
     * @return string
     * @throws Exception
     */
    public function fiatToSatoshi(int $amount, string $cryptoLongCode, string $currency = 'usd'): string
    {
        $coinGeckoClient = new CoinGeckoClient();
        $amount = self::toDollars($amount);
        $cryptoPrice = $coinGeckoClient->simple()->getPrice($cryptoLongCode, $currency);
        $cryptoPrice = sprintf("%.8f", $cryptoPrice[$cryptoLongCode][$currency]);

        return self::toSatoshi(bcdiv($amount, $cryptoPrice, 8));
    }

    /**
     * Convert crypto to USD
     *
     * @param int $satoshi
     * @param string $cryptoLongCode
     * @param string $currency
     * @return float
     * @throws Exception
     */
    public function satoshiToFiat(int $satoshi, string $cryptoLongCode, string $currency = 'usd'): float
    {
        $coinGeckoClient = new CoinGeckoClient();
        $cryptoAmount = self::toBitcoin($satoshi);
        $cryptoPrice = $coinGeckoClient->simple()->getPrice($cryptoLongCode, $currency);
        $cryptoPrice = $cryptoPrice[$cryptoLongCode][$currency];

        return bcmul($cryptoAmount, $cryptoPrice, 2);
    }
}
