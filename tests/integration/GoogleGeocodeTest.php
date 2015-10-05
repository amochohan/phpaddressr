<?php
namespace PHPAddressrTests\Integration;

use DrawMyAttention\PHPAddressr\GoogleGeocode;

class GoogleGeocodeTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->sut = new GoogleGeocode();
    }

    public function test_it_converts_a_postcode_into_a_full_address()
    {
        $fullAddress = $this->sut->getFullAddressByPostcode('SE16 2XU');

        $this->assertEquals('Surrey Quays Road', $fullAddress->street());
        $this->assertEquals('London', $fullAddress->city());
        $this->assertEquals('Greater London', $fullAddress->state());
        $this->assertEquals('United Kingdom', $fullAddress->country());
        $this->assertEquals(-0.051054, $fullAddress->longitude());
        $this->assertEquals(51.4967696, $fullAddress->latitude());
    }

    public function test_it_returns_null_if_no_results_are_found_for_a_postcode()
    {
        $fullAddress = $this->sut->getFullAddressByPostcode('BFTB325v');
        $this->assertNull($fullAddress);
    }

    public function test_it_converts_a_us_zipcode_into_a_full_address()
    {
        $fullAddress = $this->sut->getFullAddressByPostcode('90210');

        $this->assertEquals('Heather Road', $fullAddress->street());
        $this->assertEquals('Los Angeles County', $fullAddress->city());
        $this->assertEquals('California', $fullAddress->state());
        $this->assertEquals('United States', $fullAddress->country());
    }
}