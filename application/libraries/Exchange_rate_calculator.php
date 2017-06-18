<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange_rate_calculator {

    /**
     * @var Exchange_rate_repository
     */
    protected $exchange_rate_repository;

    /**
     * Currency_converter constructor.
     *
     * @param array $params
     */
    public function __construct($params)
    {
        if (empty($params['exchange_rate_repository'])) {
            throw new InvalidArgumentException('Missing exchange rate repository');
        }
        $this->exchange_rate_repository = $params['exchange_rate_repository'];
    }

    /**
     * Calculate the rate between two currencies.
     *
     * @param string $from_currency
     * @param string $to_currency
     * @return float
     */
    public function calculate($from_currency, $to_currency) {
        if (!$from = $this->exchange_rate_repository->get($from_currency)) {
            throw new InvalidArgumentException("Invalid currency {$from_currency}");
        }

        if (!$to = $this->exchange_rate_repository->get($to_currency)) {
            throw new InvalidArgumentException("Invalid currency {$to_currency}");
        }

        return floatval($from->rate) == 0 ? 0 : $to->rate / $from->rate;
    }
}