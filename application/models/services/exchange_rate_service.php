<?php

/**
 * Exchange Rate Service
 * 
 * Interacts with the exchange rate tables
 *
 */
class Exchange_rate_service extends CI_Model {

    private $table;
    public $exchange_rates = array();

    public function __construct() {
        parent::__construct();

        $this->table = 'exchange_rates';
        $this->db = $this->load->database('default', TRUE);

        // Populate the exchange_rates array from db
        $query = $this->db->select('currency, rate, updated')->where('currency <>', 'AUD')->get($this->table);
        if ($query->num_rows > 0) {
            foreach ($query->result() as $row) {
                $this->exchange_rates[$row->currency] = $row->rate;
            }
        }
    }

    /**
     * Generates a curl request to get latest rates
     * Updates database and $exchange_rates array
     *
     * @return boolean
     **/
    public function update_rates($rates) {
        $this->db = $this->load->database('default', TRUE);

        $update_successful = FALSE;
        foreach ($rates as $currency => $rate) {
            if (strlen(trim($currency)) == 3 && $rate > 0) {
                $this->db->where('currency', $currency);
                $this->db->update($this->table, array('rate' => $rate, 'updated' => date('Y-m-d H:i:s')));
                $this->exchange_rates[$currency] = $rate;
                $update_successful = TRUE;
            }
        }

        return $update_successful;
    }

    /**
     * Get a specific exchange rate for $from_currency -> $to_currency
     * Returns 0 if not found
     *
     **/
    public function get_rate($from_currency, $to_currency) {
        $from_currency = strtoupper($from_currency);
        $to_currency = strtoupper($to_currency);

        if (floatval($this->exchange_rates[$from_currency]) == 0) return 0;

        return ($this->exchange_rates[$to_currency] / $this->exchange_rates[$from_currency]);
    }
}