<?php
namespace DrawMyAttention\PHPAddressr;

use DrawMyAttention\PHPAddressr\Contracts\GeocodeContract;
use DrawMyAttention\PHPAddressr\Exceptions\MissingGeocodeApiKeyException;

class GoogleGeocode implements GeocodeContract
{
    private $apiKey = GOOGLE_API_KEY;

    public function getLatLng($address)
    {
        $results = [];
        $url = $this->buildRequest($address);
        $response = $this->sendRequest($url);
        if ($this->gotResults($response)) {
            foreach ($response['results'] as $result) {
                $results[] = [
                    'longitude' => $result['geometry']['location']['lng'],
                    'latitude' => $result['geometry']['location']['lat'],
                ];
            }
        }
        return $results;
    }

    public function sendRequest($url)
    {
        return json_decode(file_get_contents($url), true);
    }

    private function buildRequest($address)
    {
        if (!$this->isApiKeyAvailable()) {
            throw new MissingGeocodeApiKeyException();
        }
/*
        $addressAsString = '';
        foreach ($address as $attributeName => $attribute) {
            if ($this->isAttributeSet($attribute)) {
                $addressAsString .= urlencode($attribute) . ',';
            }
        }
        $addressAsString = rtrim($addressAsString, ',');
*/
        $addressAsString = $this->buildUrlEncodedCommaSeparatedStringFromArray($address);

        return 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $addressAsString . '&key=' . $this->apiKey;
    }

    public function buildUrlEncodedCommaSeparatedStringFromArray(array $data)
    {
        return $this->buildCommaSeparatedStringFromArray($data, true);
    }

    public function buildCommaSeparatedStringFromArray(array $data, $urlencode = false)
    {
        $commaSeparatedString = '';
        foreach ($data as $attributeName => $attribute) {
            if ($this->isAttributeSet($attribute)) {
                $commaSeparatedString .= ($urlencode ? urlencode($attribute) : $attribute) . ',';
            }
        }
        return ltrim(rtrim($commaSeparatedString, ','), ',');
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
        return !$this->apiKey == '' || is_null($this->apiKey);
    }

    public function getAddressByLatLng($latitude, $longitude)
    {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude;

        $response = $this->sendRequest($url);

        $addresses = [];

        if ($this->gotResults($response)) {

            $results = $response['results'];

            foreach ($results as $result) {

                $addressArray = [
                    'street' => str_replace('Dr', 'Drive', $this->getStreetFromComponents($result['address_components'])),
                    'city' => $this->getCityFromComponents($result['address_components']),
                    'state' => $this->getStateFromComponents($result['address_components']),
                    'country' => $this->getCountryFromComponents($result['address_components'])
                ];

                $address = new Address($addressArray);

                $address->shortAddress = $this->buildCommaSeparatedStringFromArray($addressArray);

                $address->setLatitude($latitude)
                    ->setLongitude($longitude);

                $addresses[] = $address;

            }
        }

        return $addresses;
    }

    public function getFullAddressByPostcode($postcode)
    {
        $latLngs = $this->getLatLng(['postcode' => $postcode]);

        $results = [];

        if (sizeof($latLngs) > 0) {
            foreach ($latLngs as $latLng) {
                $results += $this->getAddressByLatLng($latLng['latitude'], $latLng['longitude']);
            }
        }

        return array_unique($results, SORT_REGULAR);
    }

    public function getFullAddressByPostcodeAndCountry($postcode, $country)
    {
        $latLngs = $this->getLatLng(['postcode' => $postcode, 'country' => $country]);

        $results = [];

        if (sizeof($latLngs) > 0) {
            foreach ($latLngs as $latLng) {
                $results += $this->getAddressByLatLng($latLng['latitude'], $latLng['longitude']);
            }
        }

        return array_unique($results, SORT_REGULAR);
    }

    public function getStreetFromComponents($components)
    {
        foreach ($components as $component) {
            foreach ($component['types'] as $type) {
                if ($type == 'route') {
                    return $component['long_name'];
                }
            }
        }
        return null;
    }

    public function getCityFromComponents($components)
    {
        foreach ($components as $component) {
            foreach ($component['types'] as $type) {
                if ($type == 'postal_town') {
                    return $component['long_name'];
                }
            }
        }
        return null;
    }

    public function getStateFromComponents($components)
    {
        foreach ($components as $component) {
            foreach ($component['types'] as $type) {
                if ($type == 'administrative_area_level_1' || $type == 'administrative_area_level_2') {
                    return $component['long_name'];
                }
            }
        }
        return null;
    }

    public function getCountryFromComponents($components)
    {
        foreach ($components as $component) {
            foreach ($component['types'] as $type) {
                if ($type == 'country') {
                    return $component['long_name'];
                }
            }
        }
        return null;
    }


}