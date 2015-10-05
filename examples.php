<?php

require('./vendor/autoload.php');

use DrawMyAttention\PHPAddressr\GoogleGeocode;
use \DrawMyAttention\PHPAddressr\Address;

$geocode = new GoogleGeocode();

$address = new Address([
    'company' => 'Energy Aspects Ltd.',
    'building' => '1 Dock Offices',
    'street' => 'Surrey Quays Road',
    'city' => 'London',
    'postcode' => 'SE16 2XU',
    'country' => 'United Kingdom'
]);

var_dump($geocode->getLatLng($address->toArray()));

$address = new \DrawMyAttention\PHPAddressr\Address([
    'company' => 'Energy Aspects Ltd.',
    'street' => 'Surrey Quays Road',
    'postcode' => 'SE16 2XU',
]);

var_dump($geocode->getLatLng($address->toArray()));


$latLng = $geocode->getLatLng([
    'street'    => 'Surrey Quays Road',
    'city'      => 'London',
    'postcode'  => 'SE16 2XU'
]);

var_dump($latLng);


$address = $geocode->getFullAddressByPostcode('SE16 2XU');

var_dump($address);