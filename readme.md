# PHPAddressr

PHPAddressr is a framework agnostic set of methods that allows easy postcode / zipcode lookups, and conversion of 
addresses to longitude and latitude values via Google's mapping APIs.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/drawmyattention/phpaddressr/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/drawmyattention/phpaddressr/?branch=master) 
[![Build Status](https://travis-ci.org/drawmyattention/phpaddressr.svg?branch=master)](https://travis-ci.org/drawmyattention/phpaddressr)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](http://www.opensource.org/licenses/MIT)

## Installation

Composer is the easiest way to install PHPAddressr.

    composer require drawmyattention/phpaddressr
 
## Api Key

Google's geocoding API requires an API key in order to function. A key can be generated via their 
[official API website](https://developers.google.com/maps/documentation/geocoding/intro). Please keep your 
API key safe, and outside of source control.

Once you have an API key, edit the ```config.php``` file to specify your key.

## Usage

### Finding the longitude / latitude of an address

    $geocode = new DrawMyAttention\PHPAddressr\GoogleGeocode();
    
    $latLng = $geocode->getLatLng([
        'building'  => 'Dock offices'
        'street'    => 'Surrey Quays Road',
        'city'      => 'London',
        'state'     => 'Greater London',
        'postcode'  => 'SE16 2XU',
        'country'   => 'United Kingdom'
    ]);
    
    /* 
        array(2) {
          'longitude' =>
          double(-0.0507361)
          'latitude' =>
          double(51.4965262)
        }
    */
    
**Note:** Not all address values are required, however the higher number of accurate address parameters that are passed,
 the higher the accuracy of the longitude and latitude that is returned.
 
### Finding an address by postcode

    $geocode = new DrawMyAttention\PHPAddressr\GoogleGeocode();
    
    $address = $geocode->getFullAddressByPostcode('SE16 2XU');
    
    /* 
        array(4) {
          'street' =>
          string(17) "Surrey Quays Road"
          'city' =>
          string(6) "London"
          'state' =>
          string(14) "Greater London"
          'country' =>
          string(14) "United Kingdom"
        }
    */
    
## Contributing

Please submit any contributions via a pull request. Any submissions should be backed by tests in order to be merged.

## Licence

This project is open-sourced software licenced under the [MIT license](http://opensource.org/licenses/MIT).