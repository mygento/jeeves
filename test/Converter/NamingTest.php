<?php

namespace Converter;

use Mygento\Jeeves\Generators\Crud\Common;

class NamingTest extends \PHPUnit\Framework\TestCase
{
    private $converter;

    protected function setUp(): void
    {
        $this->converter = new Common();
    }

    /**
     * @dataProvider provider
     * @param string $left
     * @param string $right
     */
    public function testNaming(string $left, string $right)
    {
        $this->assertEquals($right, $this->converter->getEntityName($left));
    }

    public function provider()
    {
        return [
            ['CustomerAddress', 'CustomerAddress'],
            ['customerAddress', 'CustomerAddress'],
            ['Customer_address', 'CustomerAddress'],
            ['customer_Address', 'CustomerAddress'],
            ['Customer_Address', 'CustomerAddress'],
            ['customer_address', 'CustomerAddress'],
        ];
    }
}
