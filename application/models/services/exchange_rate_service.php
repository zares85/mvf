<?php

/**
 * Exchange Rate Service
 * 
 * Interacts with the exchange rate tables
 * Populates from openexchangerates.org
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
     * NOTE: Free plan limited to 1000 calls per month
     *
     * @return boolean
     **/
    public function update_rates() {
        $this->db = $this->load->database('default', TRUE);

        // Attempt to get latest rates from openexchangerates.org api
        $openexchangerates_url = 'https://openexchangerates.org/api/latest.json';
        $openexchangerates_app_id = ''; // Sign up for an application id with a free plan at openexchangerates.org
        $url = $openexchangerates_url . '?app_id=' . $openexchangerates_app_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = curl_exec($ch);
        var_dump($output);
        $json_result = json_decode($output);

        // If we get a valid json result - update database and $exchange_rates array
        $update_successful = FALSE;
        foreach ((array)$json_result->rates as $currency => $rate) {
            if (strlen(trim($currency)) == 3 && $rate > 0) {
                $this->db->where('currency', $currency);
                $query = $this->db->update($this->table, array('rate' => $rate, 'updated' => date('Y-m-d H:i:s')));
                $this->exchange_rates[$currency] = $rate;
                $update_successful = TRUE;
            }
        }

        curl_close($ch);

        if (!$update_successful) {
            $this->load->model('services/email_service');
            $email_service = new email_service();
            $msg = 'Exchange rates update unsuccessful from openexchangerates.org.';
            $subject = 'Exchange rates update unsuccessful';
            $email_service->send_error($msg, $subject, 'CRITICAL_ERROR');
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