<?php
namespace DrawMyAttention\PHPAddressr;

use DrawMyAttention\PHPAddressr\Exceptions\MissingCityException;
use DrawMyAttention\PHPAddressr\Exceptions\MissingCountryException;
use DrawMyAttention\PHPAddressr\Exceptions\MissingPostcodeException;
use DrawMyAttention\PHPAddressr\Exceptions\MissingStreetException;

class Address
{
    private $address = [
        'company' => null,
        'building' => null,
        'street' => null,
        'city' => null,
        'state' => null,
        'postcode' => null,
        'country' => null
    ];

    public $longitude = null;
    public $latitude = null;

    private $required = [
        'street', 'city', 'postcode', 'country'
    ];

    public function __construct($attributes = null)
    {
        if (!is_null($attributes)) {
            foreach ($attributes as $attributeName => $assignValue) {

                $value = $assignValue;
                $required = $this->defaultRequiredSetting($attributeName);

                // An array has been provided as the value for the attribute.
                // This allows the user to override the value and required
                // properties of the address attribute.
                if (is_array($assignValue)) {
                    extract($assignValue);
                }

                $this->address[$attributeName] = new Data($value, $required);
            }
        }

        // Not all of the address properies may have been provided.
        // In this case, we need to find out which properties are
        // still null and then set it up with the whatever the
        // default required setting is.
        foreach ($this->address as $attributeName => $value) {
            if (is_null($value)) {
                $this->address[$attributeName] = new Data('', $this->defaultRequiredSetting($attributeName));
            }
        }
    }

    /**
     * Verify the original address attributes are valid before looking up data.
     *
     * @return bool
     * @throws MissingCityException
     * @throws MissingCountryException
     * @throws MissingPostcodeException
     * @throws MissingStreetException
     */
    public function verify()
    {
        return $this->checkRequiredFieldsSet();
    }

    public function setCompany($companyName)
    {
        $this->address['company']->value = $companyName;
        return $this;
    }

    public function company()
    {
        return $this->address['company']->value;
    }

    public function setBuilding($buildingName)
    {
        $this->address['building']->value = $buildingName;
        return $this;
    }

    public function building()
    {
        return $this->address['building']->value;
    }

    public function setStreet($streetName)
    {
        $this->address['street']->value = $streetName;
        return $this;
    }

    public function street()
    {
        return $this->address['street']->value;
    }

    public function setCity($city)
    {
        $this->address['city']->value = $city;
        return $this;
    }

    public function city()
    {
        return $this->address['city']->value;
    }

    public function setState($state)
    {
        $this->address['state']->value = $state;
        return $this;
    }

    public function state()
    {
        return $this->address['state']->value;
    }

    public function setCountry($country)
    {
        $this->address['country']->value = $country;
        return $this;
    }

    public function country()
    {
        return $this->address['country']->value;
    }

    public function setPostcode($postcode)
    {
        $this->address['postcode']->value = $postcode;
        return $this;
    }

    public function postcode()
    {
        return $this->address['postcode']->value;
    }

    /**
     * Check if an address attribute has been set.
     *
     * @param string $attribute
     * @return bool
     */
    private function isAttributeEmpty($attribute)
    {
        return $this->address[$attribute]->value == '';
    }

    /**
     * Ensure that all required address fields are present.
     *
     * @throws MissingCityException
     * @throws MissingCountryException
     * @throws MissingPostcodeException
     * @throws MissingStreetException
     */
    private function checkRequiredFieldsSet()
    {
        if ($this->isAttributeEmpty('country')) {
            throw new MissingCountryException();
        }
        if ($this->isAttributeEmpty('postcode')) {
            throw new MissingPostcodeException();
        }
        if ($this->isAttributeEmpty('city')) {
            throw new MissingCityException();
        }
        if ($this->isAttributeEmpty('street')) {
            throw new MissingStreetException();
        }
        return true;
    }

    /**
     * Check if an address attribute must be assigned before a lookup can be performed.
     *
     * @param string $attribute Name of the address attribute.
     * @return bool
     */
    public function isRequired($attribute)
    {
        return $this->address[$attribute]->required;
    }

    public function setAttributeAsRequired($attribute, $required = true)
    {
        $this->address[$attribute]->required = $required;
        return $this;
    }

    /**
     * @param $attributeName
     * @return bool
     */
    private function defaultRequiredSetting($attributeName)
    {
        return in_array($attributeName, $this->required);
    }

    public function toArray()
    {
        $result = [];
        foreach ($this->address as $attribute => $values) {
            $result[$attribute] = $values->value;
        }
        $result['longitude'] = $this->longitude;
        $result['latitude'] = $this->latitude;
        return $result;
    }
}
