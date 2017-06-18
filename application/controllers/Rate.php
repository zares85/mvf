<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rate controller
 *
 * @property CI_Output output
 * @property CI_Loader load
 * @property CI_Config config
 * @property Email_service email_service
 * @property Exchange_rate_repository exchange_rate_repository
 * @property Exchange_rate_provider exchange_rate_provider
 * @property Exchange_rate_calculator exchange_rate_calculator
 * @property Exchange_rate_hydrator exchange_rate_hydrator
 */
class Rate extends CI_Controller {


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
            'Rate' => 'index'
        ]));
    }

    /**
     * Update the exchange_rates in the repository.
     */
    public function update_exchange_rates()
    {
        try {
            $this->config->load('exchange_rate', true);
            $this->load->library('exchange_rate_hydrator');
            $this->load->library('exchange_rate_provider', [
                'app_id' => $this->config->item('app_id', 'exchange_rate')
            ]);

            $rates = $this->exchange_rate_provider->get_rates();
            $rates = $this->exchange_rate_hydrator->hydrate($rates, Rate_entity::class);
            foreach ($rates as $rate) {
                $this->exchange_rate_repository->save($rate);
            }

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

            $this->load->library('exchange_rate_calculator', [
                'exchange_rate_repository' => $this->exchange_rate_repository
            ]);

            $rate = $this->exchange_rate_calculator->calculate($from_currency, $to_currency);

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
