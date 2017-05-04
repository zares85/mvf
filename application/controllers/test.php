<?php
/**
 * Test controller
 */
class Test extends CI_Controller {

    public function index()
    {
        Header("Content-type: application:json");
        echo json_encode(array('test' => 'index'));
    }

    /**
     * Update the mvf.exchange_rates
     * TODO: This probably shouldn't be run from lead_platform?
     *
     */
    public function update_exchange_rates() {
        $this->load->model('services/exchange_rate_service');
        $exchange_rate_service = new exchange_rate_service();

        Header("Content-type: application:json");
        $result = $exchange_rate_service->update_rates();
        echo json_encode(array(
            'status' => $result ? "OK" : "FAILED",
        ));
        echo PHP_EOL;
    }

    public function convert_currency($amount, $from_currency, $to_currency = 'GBP')
    {
        $this->load->model('services/exchange_rate_service');
        $exchange_rate_service = new exchange_rate_service();

        Header("Content-type: application:json");
        $rate = $exchange_rate_service->get_rate($from_currency, $to_currency);
        echo json_encode(array(
            'from_currency' => $from_currency,
            'to_currency' => $to_currency,
            'exchange_rate' => $rate,
            'original_amount' => $amount,
            'converted_amount' => $amount * $rate,
        ));
        echo PHP_EOL;
    }

}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */
