<?php

defined('BASEPATH') OR exit('No direct script access allowed');

interface Exchange_rate_repository_interface {

    /**
     * Insert or update a rate into database.
     *
     * @param Exchange_rate_entity $rate
     */
    public function save(Exchange_rate_entity $rate);


    /**
     * Return a rate give a currency.
     *
     * @param string $currency
     * @return Exchange_rate_entity|null
     */
    public function get($currency);
}