<?php

defined('BASEPATH') OR exit('No direct script access allowed');

interface Exchange_rate_repository_interface {

    /**
     * Insert or update a rate into database.
     *
     * @param Rate_entity $rate
     */
    public function save(Rate_entity $rate);


    /**
     * Return a rate give a currency.
     *
     * @param string $currency
     * @return Rate_entity|null
     */
    public function get($currency);
}