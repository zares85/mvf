<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Exchange_rate_repository
 *
 * @property CI_DB db
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
            if (!$this->db->where('currency', $rate->currency)->update($this->table, $rate)) {
                throw new RuntimeException("Error updating currency {$rate->currency} to {$rate->rate}");
            }
        } else {
            if (!$this->db->insert($this->table, $rate)) {
                throw  new RuntimeException("Error adding currency {$rate->currency} with rate {$rate->rate}");
            }
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