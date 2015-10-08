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
        $results = $this->sut->getFullAddressByPostcode('SE16 2XU');

        $this->assertEquals('Surrey Quays Road', $results[0]->street());
        $this->assertEquals('London', $results[0]->city());
        $this->assertEquals('Greater London', $results[0]->state());
        $this->assertEquals('United Kingdom', $results[0]->country());
        $this->assertEquals(-0.051054, $results[0]->longitude());
        $this->assertEquals(51.4967696, $results[0]->latitude());
    }

    public function test_it_returns_an_empty_array_if_no_results_are_found_for_a_postcode()
    {
        $fullAddress = $this->sut->getFullAddressByPostcode('BFTB325v');
        $this->assertCount(0, $fullAddress);
    }

    public function test_it_converts_a_us_zipcode_into_a_full_address()
    {
        $results = $this->sut->getFullAddressByPostcode('90210');
        $this->assertEquals('Heather Road', $results[0]->street());
        $this->assertEquals('United States', $results[0]->country());
    }

    public function test_it_returns_multiple_addresses_when_more_than_one_match_is_found()
    {
        $results = $this->sut->getFullAddressByPostcode('NG19');
        $this->assertCount(5, $results);
    }

    public function test_it_searches_using_a_postcode_and_country()
    {
        $results = $this->sut->getFullAddressByPostcodeAndCountry('1600', 'Philippines');

        $this->assertEquals('Philippines', $results[0]->country());

    }

    private function outputShortAddresses($results)
    {
        foreach($results as $result) {
            var_dump($result->shortAddress);
        }
        exit();
    }
}