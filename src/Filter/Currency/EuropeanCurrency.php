<?php

declare(strict_types=1);

namespace settermjd\Filter\Currency;

use Zend\Filter\Exception\InvalidArgumentException;
use Zend\Filter\FilterInterface;

/**
 * Class EuropeanCurrency
 *
 * This class is designed to Filter and return the currency values
 * in CSV export files which the German bank, ING DiBa, creates
 * when a personal banking customer exports records from their
 * bank account.
 *
 * From the samples that I've seen, the currency values are written
 * in one of three forms (both positive and negative:
 *
 * - 1234,21
 * - 1234,2
 * - 1234,
 *
 * So far, even if there is no fractional/decimal amount available
 * for the transaction, the trailing decimal separator (,) is always
 * written in the amount. This class has been written to Filter based
 * on each of these formats. It has not been written to take account
 * of anything more custom.
 *
 * @package Filter\Currency
 */
class EuropeanCurrency implements FilterInterface
{
    const SEPARATOR = ',';
    const PATTERN = '/^[-]?([\d]{1,3}[\.]?)+(,[\d]{0,2})?$/';

    /**
     * Filter a currency value
     * @param mixed $value
     * @return string
     */
    public function filter($value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Supplied currency value does not match the required format.');
        }

        // Get the integer component
        $integer = $this->getIntegerComponent($value);

        // Get the fractional component
        $fraction = $this->getFractionalComponent($value);

        if (strlen($fraction) < 2) {
            $fraction = str_pad($fraction, 2, '0', STR_PAD_RIGHT);
        }

        return str_replace('.', '', $integer) . $fraction;
    }

    /**
     * Is the number a valid european currency string
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return (preg_match(self::PATTERN, $value) == 1);
    }

    /**
     * Get the integer component of the supplied value
     * @param string $value
     * @return bool|string
     */
    public function getIntegerComponent($value)
    {
        $decimalSeparator = $this->getDecimalSeparator($value);
        return ($decimalSeparator !== false && $decimalSeparator !== 0)
            ? substr($value, 0, $decimalSeparator)
            : $value;
    }

    /**
     * Get the fractional component of the supplied value
     * @param string $value
     * @return bool|string
     */
    public function getFractionalComponent($value)
    {
        $decimalSeparator = $this->getDecimalSeparator($value);
        return ($decimalSeparator !== false && $decimalSeparator !== 0)
            ? substr($value, $decimalSeparator + 1, 2)
            : '00';
    }

    /**
     * @param string $value
     * @return int|bool
     */
    public function getDecimalSeparator($value)
    {
        return stripos($value, self::SEPARATOR);
    }
}
