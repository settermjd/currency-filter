<?php

declare(strict_types=1);

namespace settermjd\Filter\Currency;

use Zend\Filter\Exception\InvalidArgumentException;
use Zend\Filter\FilterInterface;

/**
 * Class CurrencyStringToInteger
 *
 * This class converts a European currency string to an integer.
 * It can convert all the standard currency strings, a range of
 * samples are available below, to an integer representation. This
 * was largely motivated by having to use such strings with the
 * money\money library.
 *
 * Examples:
 *
 * '-3.500,01'
 * '-3500,00'
 * '-4.501,2'
 * '1,'
 * '1,01'
 * '1,2'
 * '1.00,00'
 * '1.887.761.234'
 * '1.887.761.234,01'
 * '1.887.761.234,2'
 * '1000,00'
 * '1234.24'
 * '1887761234,'
 * '1887761234,2'
 * '1887761234,2'
 * '1887761234,21'
 * '20,21'
 * '21,02'
 * '21,2'
 *
 * @package Filter\Currency
 */
class CurrencyStringToInteger implements FilterInterface
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
