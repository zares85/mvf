<?php

class Exchange_rate_calculator_test extends \PHPUnit\Framework\TestCase {

    /**
     * @var Exchange_rate_calculator
     */
    protected $erc;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $err;

    public function setUp()
    {
        $this->err = $this->getMockBuilder(Exchange_rate_repository_interface::class)->getMock();
        $this->erc = new Exchange_rate_calculator([
            'exchange_rate_repository' => $this->err,
        ]);
    }

    /**
     * Test a basic rate calculation
     */
    public function testBasic()
    {
        $rate1 = new Rate_entity;
        $rate1->currency = 'EUR';
        $rate1->rate = 0.8;

        $rate2 = new Rate_entity;
        $rate2->currency = 'GBP';
        $rate2->rate = 0.6;

        $this->err->method('get')->will($this->returnValueMap([
            [$rate1->currency, $rate1],
            [$rate2->currency, $rate2],
        ]));

        $this->assertEquals(
            $this->erc->calculate($rate1->currency, $rate2->currency),
            $rate2->rate / $rate1->rate
        );
    }

    /**
     * Test a divide by zero
     */
    public function testRateZero() {

        $rate1 = new Rate_entity;
        $rate1->currency = 'EUR';
        $rate1->rate = 0;

        $rate2 = new Rate_entity;
        $rate2->currency = 'GBP';
        $rate2->rate = 0.6;

        $this->err->method('get')->will($this->returnValueMap([
            [$rate1->currency, $rate1],
            [$rate2->currency, $rate2],
        ]));

        $this->assertEquals(
            $this->erc->calculate($rate1->currency, $rate2->currency),
            0
        );
    }

    /**
     * Test an invalid currency.
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidCurrency() {

        $rate1 = new Rate_entity;
        $rate1->currency = 'EUR';
        $rate1->rate = 0.8;

        $this->err->method('get')->will($this->returnValueMap([
            [$rate1->currency, $rate1],
            ['ERR', null],
        ]));

        $this->erc->calculate($rate1->currency, 'ERR');
    }

    /**
     * Test an invalid rate
     *
     * @expectedException RuntimeException
     */
    public function testInvalidRate() {
        $rate1 = new Rate_entity;
        $rate1->currency = 'EUR';
        $rate1->rate = 0.8;

        $rate2 = new Rate_entity;
        $rate2->currency = 'GBP';
        $rate2->rate = 'invalid';

        $this->err->method('get')->will($this->returnValueMap([
            [$rate1->currency, $rate1],
            [$rate2->currency, $rate2],
        ]));

        $this->erc->calculate($rate1->currency, $rate2->currency);
    }
}