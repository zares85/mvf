<?php

/**
 * Exchange Rate Model
 * 
 * Interacts with the exchange rate tables
 *
 */
class Exchange_rates extends CI_Model {

    private $table;
    public $exchange_rates = array();

    public function __construct() {
        parent::__construct();

        $this->table = strtolower(__CLASS__);
        $this->db = $this->load->database('default', TRUE);
    }

    /**
     * Generates a curl request to get latest rates
     * Updates database and $exchange_rates array
     *
     * @return boolean
     **/
    public function update_rates($rates) {
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
        $from = $this->db
            ->select('rate')
            ->where('currency', strtoupper($from_currency))
            ->get($this->table)
            ->row()->rate;

        $to = $this->db
            ->select('rate')
            ->where('currency', strtoupper($to_currency))
            ->get($this->table)
            ->row()->rate;

        return floatval($from) == 0 ? 0 : $to / $from;
    }
}