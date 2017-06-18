<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Exchange_rate_provider
 *
 * Load rates from openexchangerates.org api
 * Require an Application ID to work
 * Sign up for an application id with a free plan at openexchangerates.org
 * NOTE: Free plan limited to 1000 calls per month
 */
class Exchange_rate_provider {

    /**
     * @var string
     */
    protected $url = 'https://openexchangerates.org/api/latest.json';

    /**
     * @var string
     */
    protected $app_id;

    /**
     * Exchange_rate_provider constructor.
     */
    public function __construct($params)
    {
        if (empty($params['app_id'])) {
            throw new InvalidArgumentException('Missing application id');
        }
        $this->app_id = $params['app_id'];
    }

    /**
     * Return current rates from openexchangerates.org
     *
     * @return array
     */
    public function get_rates()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->url}?app_id={$this->app_id}");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($data, true);

        return $decoded['rates'];
    }
}