<?php

/**
 * Test controller
 */
class Test extends CI_Controller {


    /**
     * Test constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
    }

    /**
     * Dummy index method.
     */
    public function index()
    {
        $this->output->set_output(json_encode([
            'test' => 'index'
        ]));
    }

    /**
     * Update the mvf.exchange_rates
     */
    public function update_exchange_rates()
    {
        try {
            $this->config->load('exchange_rate', true);
            $this->load->model('exchange_rates');
            $this->load->library('exchange_rate_provider', [
                'app_id' => $this->config->item('app_id', 'exchange_rate')
            ]);

            $rates = $this->exchange_rate_provider->get_rates();
            $this->exchange_rates->update_rates($rates);

            $this->output->set_output(json_encode([
                'status' => 'OK'
            ]));
        } catch (Exception $e) {
            $this->output->set_status_header(500)->set_output(json_encode([
                'status' => 'FAILED',
                'error'  => $e->getMessage(),
            ]));
            $this->load->library('email_service');
            $this->email_service->send_error(
                'Exchange rates update unsuccessful from openexchangerates.org.',
                'Exchange rates update unsuccessful',
                'CRITICAL_ERROR'
            );
        }
    }

    /**
     * Convert an amount between two currencies.
     *
     * @param int $amount
     * @param string $from_currency
     * @param string $to_currency
     */
    public function convert_currency($amount, $from_currency, $to_currency = 'GBP')
    {
        try {
            if (!ctype_digit($amount)) {
                throw new InvalidArgumentException("Invalid amount {$amount}");
            }

            $this->load->model('exchange_rates');
            $rate = $this->exchange_rates->get_rate($from_currency, $to_currency);

            $this->output->set_output(json_encode([
                'from_currency'    => $from_currency,
                'to_currency'      => $to_currency,
                'exchange_rate'    => $rate,
                'original_amount'  => $amount,
                'converted_amount' => $amount * $rate,
            ]));
        } catch (Exception $e) {
            $this->output->set_status_header(500)->set_output(json_encode([
                'error' => $e->getMessage(),
            ]));
        }
    }

}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */
