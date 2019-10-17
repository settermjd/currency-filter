<?php

declare(strict_types=1);

namespace INGDiBa\Filter;

use Zend\Filter\Exception\InvalidArgumentException;
use Zend\Filter\FilterInterface;

/**
 * Class Currency
 *
 * This class is designed to filter and return the currency values
 * in CSV export files which the German bank, ING DiBa, creates
 * when a personal banking customer exports records from their
 * bank account.
 *
 * From the samples that I've seen, the currency values are written
 * in one of three forms:
 *
 * - 1234,21
 * - 1234,2
 * - 1234,
 *
 * So far, even if there is no fractional/decimal amount available
 * for the transaction, the trailing decimal separator (,) is always
 * written in the amount. This class has been written to filter based
 * on each of these formats. It has not been written to take account
 * of anything more custom.
 *
 * @package INGDiBa\Filter
 */
class Currency implements FilterInterface
{
    const SEPARATOR = ',';

    /**
     * Filter a currency value
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $value = (string)$value;

        if (strstr($value, self::SEPARATOR) === false) {
            throw new InvalidArgumentException('Supplied currency value does not match the required format.');
        }

        $integer = substr($value, 0, (strripos($value, self::SEPARATOR)));
        $fraction = substr($value, (strripos($value, self::SEPARATOR) + 1));

        if (strlen($fraction) < 2) {
            $fraction = str_pad($fraction, 2, '0', STR_PAD_RIGHT);
        }

        return $integer . $fraction;
    }
}
