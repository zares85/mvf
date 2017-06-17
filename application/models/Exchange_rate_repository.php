<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Exchange_rate_repository
 */
class Exchange_rate_repository extends CI_Model {

    /**
     * Table name
     * @var string
     */
    protected $table = 'exchange_rates';

    /**
     * Insert or update a rate into database.
     *
     * @param Rate_entity $rate
     */
    public function save(Rate_entity $rate)
    {
        $exists = $this->db->where('currency', $rate->currency)->get($this->table)->num_rows() > 0;

        if ($exists) {
            return $this->db->where('currency', $rate->currency)->update($this->table, $rate);
        } else {
            return $this->db->insert($this->table, $rate);
        }
    }

    /**
     * Return a rate give a currency.
     *
     * @param string $currency
     * @return Rate_entity|null
     */
    public function get($currency) {
        return $this->db->where('currency', $currency)->get($this->table)->row(0, Rate_entity::class);
    }
}