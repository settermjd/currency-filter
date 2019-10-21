<?php

declare(strict_types=1);

namespace settermjd\Filter\Currency\Test\Currency;

use settermjd\Filter\Currency\CurrencyStringToInteger;
use PHPUnit\Framework\TestCase;
use Zend\Filter\Exception\InvalidArgumentException;

/**
 * Class CurrencyStringToIntegerTest
 * @package Filter\Currency\Test
 * @coversDefaultClass \settermjd\Filter\Currency\CurrencyStringToInteger
 */
class CurrencyStringToIntegerTest extends TestCase
{
    /**
     * @param string $testData
     * @param string $validData
     * @dataProvider dataProvider
     */
    public function testCanCorrectlyFilterInputData($testData, $validData)
    {
        $filter = new CurrencyStringToInteger();
        $this->assertSame($validData, $filter->filter($testData));
    }

    public function dataProvider()
    {
        return [
            ['-0,55', -55],
            ['0,55', 55],
            ['1,01', 101],
            ['1,2', 120],
            ['1,', 100],
            ['20,21', 2021],
            ['21,02', 2102],
            ['21,2', 2120],
            ['1000,00', 100000],
            ['1.00,00', 10000],
            ['-3500,00', -350000],
            ['-3.500,01', -350001],
            ['-4.501,2', -450120],
            ['1.887.761.234', 188776123400],
            ['1.887.761.234,01', 188776123401],
            ['1.887.761.234,2', 188776123420],
            ['1887761234,2', 188776123420],
            ['1887761234,21', 188776123421],
            ['1887761234,2', 188776123420],
            ['1887761234,', 188776123400],
            ['1234.24', 12342400],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testThrowsExceptionIfStringDoesNotMatchTheRequiredPattern($data)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Supplied currency value does not match the required format.');
        $filter = new CurrencyStringToInteger();
        $filter->filter($data);
    }

    public function invalidDataProvider()
    {
        return [
            ['1234,43987'],
            ['Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
            [null],
            [''],
            [false],
        ];
    }
}
