<?php

declare(strict_types=1);

namespace settermjd\INGDiBa\Test\Filter;

use settermjd\INGDiBa\Filter\Currency;
use PHPUnit\Framework\TestCase;
use Zend\Filter\Exception\InvalidArgumentException;

/**
 * Class CurrencyTest
 * @package INGDiBaTest\Filter
 * @coversDefaultClass settermjd\INGDiBa\Filter\Currency
 */
class CurrencyTest extends TestCase
{
    /**
     * @param string $testData
     * @param string $validData
     * @dataProvider dataProvider
     */
    public function testCanCorrectlyFilterInputData($testData, $validData)
    {
        $filter = new Currency();
        $this->assertEquals($validData, $filter->filter($testData));
    }

    public function dataProvider()
    {
       return [
           ['1000,00', 100000],
           ['1234,4', 123440],
           ['1234,', 123400],
           ['887761234,', 88776123400],
       ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testThrowsExceptionIfStringDoesNotContainASeparator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Supplied currency value does not match the required format.');
        $filter = new Currency();
        $filter->filter(1234);
    }

    public function invalidDataProvider()
    {
        return [
            [1234],
            ['1234'],
            [1234.43],
            ['1234.43'],
            ['1234,43987'],
            [-1234],
            [-1234.54],
            [-1234.2],
        ];
    }
}