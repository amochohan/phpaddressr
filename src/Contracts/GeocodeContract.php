<?php
namespace DrawMyAttention\PHPAddressr\Contracts;

interface GeocodeContract
{
    public function getLatLng($address);
    public function getAddressByLatLng($latitude, $longitude);
    public function gotResults($response);
}