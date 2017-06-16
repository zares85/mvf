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
        $this->load->model('services/exchange_rate_service');
        $this->load->library('exchange_rate_provider', [
            'app_id' => $this->config->item('app_id', 'exchange_rate')
        ]);

        $exchange_rate_service = new exchange_rate_service();
        $rates = $this->exchange_rate_provider->get_rates();
        $result = $exchange_rate_service->update_rates($rates);

        if (!$result) {
            $this->load->model('services/email_service');
            $email_service = new email_service();
            $msg = 'Exchange rates update unsuccessful from openexchangerates.org.';
            $subject = 'Exchange rates update unsuccessful';
            $email_service->send_error($msg, $subject, 'CRITICAL_ERROR');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'status' => $result ? "OK" : "FAILED"
        ]));
    }

    public function convert_currency($amount, $from_currency, $to_currency = 'GBP')
    {
        $this->load->model('services/exchange_rate_service');
        $exchange_rate_service = new exchange_rate_service();

        $rate = $exchange_rate_service->get_rate($from_currency, $to_currency);

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
