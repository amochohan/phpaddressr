<?php
namespace DrawMyAttention\PHPAddressr;

use DrawMyAttention\PHPAddressr\Contracts\GeocodeContract;
use DrawMyAttention\PHPAddressr\Exceptions\MissingGeocodeApiKeyException;

class GoogleGeocode implements GeocodeContract
{
    private $apiKey = GOOGLE_API_KEY;

    public function getLatLng($address)
    {
        $url = $this->buildRequest($address);
        $response = $this->sendRequest($url);
        if ($this->gotResults($response)) {
            return [
                'longitude' => $response['results'][0]['geometry']['location']['lng'],
                'latitude' => $response['results'][0]['geometry']['location']['lat']
            ];
        }
    }

    public function sendRequest($url)
    {
        return json_decode(file_get_contents($url),true);
    }

    private function buildRequest($address)
    {
        if (! $this->isApiKeyAvailable()) {
            throw new MissingGeocodeApiKeyException();
        }

        $addressAsString = '';
        foreach($address as $attributeName => $attribute) {
            if ($this->isAttributeSet($attribute)) {
                $addressAsString .= urlencode($attribute) . ',';
            }
        }
        $addressAsString = rtrim($addressAsString, ',');

        return 'https://maps.googleapis.com/maps/api/geocode/json?address='.$addressAsString.'&key=' . $this->apiKey;
    }

    /**
     * @param $attribute
     * @return bool
     */
    private function isAttributeSet($attribute)
    {
        return $attribute != '' || !is_null($attribute);
    }

    public function gotResults($response)
    {
        return $response['status'] == 'OK';
    }

    public function setApiKey($key)
    {
        $this->apiKey = $key;
        return $this;
    }

    /**
     * @return bool
     */
    private function isApiKeyAvailable()
    {
        return ! $this->apiKey == '' || is_null($this->apiKey);
    }

    public function getAddressByLatLng($latitude, $longitude)
    {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude;

        $response = $this->sendRequest($url);

        if ($this->gotResults($response)) {

            $address = $response['results'][0]['address_components'];

            return [
                'street'    => str_replace('Dr', 'Drive', $address[1]['long_name']),
                'city'      => $address[3]['long_name'],
                'state'     => $address[4]['long_name'],
                'country'   => $address[5]['long_name']
            ];
        }
        return null;
    }

    public function getFullAddressByPostcode($postcode)
    {
        $latLng = $this->getLatLng(['postcode' => $postcode]);

        if ($latLng) {
            return $this->getAddressByLatLng($latLng['latitude'], $latLng['longitude']);
        }

        return null;
    }

}