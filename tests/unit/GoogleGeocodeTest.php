<?php
namespace PHPAddressrTests\Unit;

use DrawMyAttention\PHPAddressr\GoogleGeocode;

class GoogleGeocodeTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_gets_a_longitude_and_latitude_from_google_api_using_a_real_address()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $geocode->expects($this->once())
            ->method('sendRequest')
            ->willReturn($this->validLatLngResponse());

        $results = $geocode->getLatLng([
            'building'  => '1 Dock Offices',
            'street'    => 'Surrey Quays Road',
            'city'      => 'London',
            'postcode'  => 'SE16 2XU',
            'country'   => 'United Kingdom'
        ]);

        $this->assertCount(1, $results);
        $this->assertEquals(321, $results[0]['longitude']);
        $this->assertEquals(123, $results[0]['latitude']);
    }

    public function test_it_handles_no_results_when_a_long_lat_cannot_be_found()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $geocode->expects($this->once())
            ->method('sendRequest')
            ->willReturn($this->invalidResponse());

        $results = $geocode->getLatLng([
            'building'  => 'Some building',
            'street'    => 'This is not a real street',
            'city'      => 'Crazy town',
            'postcode'  => '123ABC098',
            'country'   => 'Mars'
        ]);

        $this->assertCount(0, $results);
    }

    public function test_it_builds_a_request_url_from_address_data()
    {
        $lookup = new GoogleGeocode();

        $lookup->setApiKey(123);

        $url = $this->callPrivateMethod($lookup, 'buildRequest', [[
            'company'   => 'Energy Aspects Ltd.',
            'building'  => 'Dock Offices',
            'street'    => 'Surrey Quays Road',
            'city'      => 'London',
            'postcode'  => 'SE16 2XU',
            'country'   => 'United Kingdom'
        ]]);

        $this->assertEquals('https://maps.googleapis.com/maps/api/geocode/json?address=Energy+Aspects+Ltd.,Dock+Offices,Surrey+Quays+Road,London,SE16+2XU,United+Kingdom&key=123', $url);
    }

    /**
     * @expectedException DrawMyAttention\PHPAddressr\Exceptions\MissingGeocodeApiKeyException
     */
    public function test_it_throws_an_exception_when_an_api_key_hasnt_been_set()
    {
        $lookup = new GoogleGeocode();
        $lookup->setApiKey('');

        $this->callPrivateMethod($lookup, 'buildRequest', [[
            'company'   => 'Energy Aspects Ltd.',
            'building'  => 'Dock Offices',
            'street'    => 'Surrey Quays Road',
            'city'      => 'London',
            'postcode'  => 'SE16 2XU',
            'country'   => 'United Kingdom'
        ]]);

    }

    public function test_it_gets_an_address_by_lat_long()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $geocode->expects($this->once())
            ->method('sendRequest')
            ->willReturn($this->validAddressResponse());

        $results = $geocode->getAddressByLatLng('55.378051', '-3.435973');

        $this->assertCount(1, $results);
        $this->assertInstanceOf('DrawMyAttention\PHPAddressr\Address', $results[0]);

        $this->assertEquals('Surrey Quays Road', $results[0]->street());
        $this->assertEquals('London', $results[0]->city());
        $this->assertEquals('Greater London', $results[0]->state());
        $this->assertEquals('United Kingdom', $results[0]->country());
    }

    public function test_it_gets_an_address_by_postcode()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $geocode->expects($this->at(0))
            ->method('sendRequest')
            ->with('https://maps.googleapis.com/maps/api/geocode/json?address=SE16+2XU&key=123')
            ->willReturn($this->validLatLngResponse());

        $geocode->expects($this->at(1))
            ->method('sendRequest')
            ->with('http://maps.googleapis.com/maps/api/geocode/json?latlng=123,321')
            ->willReturn($this->validAddressResponse());

        $geocode->setApiKey('123');
        $results = $geocode->getFullAddressByPostcode('SE16 2XU');

        $this->assertCount(1, $results);

        $this->assertEquals('Surrey Quays Road', $results[0]->street());
        $this->assertEquals('London', $results[0]->city());
        $this->assertEquals('Greater London', $results[0]->state());
        $this->assertEquals('United Kingdom', $results[0]->country());
    }

    public function test_it_returns_an_empty_array_when_an_address_cannot_be_found_by_postcode()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $geocode->expects($this->once())
            ->method('sendRequest')
            ->with('https://maps.googleapis.com/maps/api/geocode/json?address=1NV4L1DP05TC0DE&key=123')
            ->willReturn($this->invalidResponse());

        $geocode->setApiKey('123');

        $address = $geocode->getFullAddressByPostcode('1NV4L1DP05TC0DE');

        $this->assertInternalType('array', $address);
        $this->assertCount(0, $address);
    }

    public function test_it_returns_an_empty_array_when_an_address_cannot_be_found_by_lat_lng()
    {
        $geocode = $this->getMock('DrawMyAttention\PHPAddressr\GoogleGeocode', ['sendRequest']);

        $invalidLat = '12451.215d';
        $invalidLng = '125b.1251';

        $geocode->expects($this->once())
            ->method('sendRequest')
            ->with('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$invalidLat.',' . $invalidLng)
            ->willReturn($this->invalidResponse());

        $address = $geocode->getAddressByLatLng($invalidLat, $invalidLng);

        $this->assertInternalType('array', $address);
        $this->assertCount(0, $address);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function callPrivateMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private function validLatLngResponse()
    {
        return [
            'status' => 'OK',
            'results' => [[
                'geometry' => [
                    'location' => [
                        'lat' => 123,
                        'lng' => 321
                    ]
                ]
            ]]
        ];
    }

    private function validAddressResponse()
    {
        return [
            'status' => 'OK',
            'results' => [[
                'address_components' => [
                    ['long_name' => '21', 'types' => ['street_number']],
                    ['long_name' => 'Surrey Quays Road', 'types' => ['route']],
                    ['long_name' => 'London', 'types' => ['postal_town', 'locality', 'political']],
                    ['long_name' => 'London', 'types' => ['political']],
                    ['long_name' => 'Greater London', 'types' => ['administrative_area_level_2', 'administrative_area_level_1', 'political']],
                    ['long_name' => 'United Kingdom', 'types' => ['country', 'political']],
                    ['long_name' => 'SE16', 'types' => ['postal_code']],
                ]
            ]]
        ];
    }

    private function invalidResponse()
    {
        return ['results' => [], 'status' => 'ZERO_RESULTS'];
    }
}