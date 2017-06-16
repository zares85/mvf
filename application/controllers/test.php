<?php
/**
 * Test controller
 */
class Test extends CI_Controller {

    public function index()
    {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'test' => 'index'
        ]));
    }

    /**
     * Update the mvf.exchange_rates
     *
     */
    public function update_exchange_rates() {
        $this->config->load('exchange_rate', true);
        $this->load->model('exchange_rates');
        $this->load->library('exchange_rate_provider', [
            'app_id' => $this->config->item('app_id', 'exchange_rate')
        ]);

        $rates = $this->exchange_rate_provider->get_rates();
        $result = $this->exchange_rates->update_rates($rates);

        if (!$result) {
            $this->load->library('email_service');
            $msg = 'Exchange rates update unsuccessful from openexchangerates.org.';
            $subject = 'Exchange rates update unsuccessful';
            $this->email_service->send_error($msg, $subject, 'CRITICAL_ERROR');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'status' => $result ? "OK" : "FAILED"
        ]));
    }

    public function convert_currency($amount, $from_currency, $to_currency = 'GBP')
    {
        $this->load->model('exchange_rates');

        $rate = $this->exchange_rates->get_rate($from_currency, $to_currency);

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'from_currency'    => $from_currency,
            'to_currency'      => $to_currency,
            'exchange_rate'    => $rate,
            'original_amount'  => $amount,
            'converted_amount' => $amount * $rate,
        ]));
    }

}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */
