<?php

/**
 * Exchange Rate Model
 * 
 * Interacts with the exchange rate tables
 *
 */
class Exchange_rates extends CI_Model {

    /**
     * Table name
     * @var string
     */
    private $table;

    /**
     * Exchange_rates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = strtolower(__CLASS__);
        $this->db = $this->load->database('default', TRUE);
    }

    /**
     * Updates database rates
     *
     * @param $rates
     * @throws InvalidArgumentException if currency or rate is not valid.
     */
    public function update_rates($rates)
    {
        foreach ($rates as $currency => $rate) {
            if (strlen(trim($currency)) != 3) {
                throw new InvalidArgumentException("Invalid currency {$currency}");
            }
            if (!$rate > 0) {
                throw new InvalidArgumentException("Invalid rate {$rate}");
            }
            $this->db->where('currency', $currency);
            $this->db->update($this->table, array('rate' => $rate, 'updated' => date('Y-m-d H:i:s')));
        }
    }

    /**
     * Get a specific exchange rate for $from_currency -> $to_currency
     *
     * @param string $from_currency
     * @param string $to_currency
     * @return float|int
     * @throws InvalidArgumentException if any of the give currencies is invalid
     */
    public function get_rate($from_currency, $to_currency)
    {
        $from = $this->db
            ->select('rate')
            ->where('currency', strtoupper($from_currency))
            ->get($this->table)
            ->row(0, Rate_entity::class);

        if (!$from) {
            throw new InvalidArgumentException("Invalid currency {$from_currency}");
        }

        $to = $this->db
            ->select('rate')
            ->where('currency', strtoupper($to_currency))
            ->get($this->table)
            ->row(0, Rate_entity::class);

        if (!$to) {
            throw new InvalidArgumentException("Invalid currency {$to_currency}");
        }

        return floatval($from->rate) == 0 ? 0 : $to->rate / $from->rate;
    }
}