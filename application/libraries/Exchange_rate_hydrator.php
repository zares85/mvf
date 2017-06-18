<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange_rate_hydrator {

    public function hydrate($rates, $class)
    {
        $objects = array();

        foreach ($rates as $currency => $rate) {
            $object = new $class;
            $object->currency = $currency;
            $object->rate = $rate;
            $object->updated = date('Y-m-d H:i:s');
            $objects[] = $object;
        }

        return $objects;
    }
}