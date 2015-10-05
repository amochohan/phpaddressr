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

Alternatively, you can set the Api key using the ```setApiKey()``` method in the GoogleGeocode class.

## Usage

### Finding the longitude / latitude of an address

    $geocode = new DrawMyAttention\PHPAddressr\GoogleGeocode();
    
    // Optional, if a key hasn't been provided in the config file.
    $geocode->setApiKey('123abc');
    
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
    
    // An Address instance is returned.
    
    /* 
        class DrawMyAttention\PHPAddressr\Address#4 (4) {
          private $address =>
          array(7) {
            'company' =>
            class DrawMyAttention\PHPAddressr\Data#9 (3) {
              public $required =>
              bool(false)
              public $value =>
              string(0) ""
              public $updated =>
              string(0) ""
            }
            'building' =>
            class DrawMyAttention\PHPAddressr\Data#10 (3) {
              public $required =>
              bool(false)
              public $value =>
              string(0) ""
              public $updated =>
              string(0) ""
            }
            'street' =>
            class DrawMyAttention\PHPAddressr\Data#5 (3) {
              public $required =>
              bool(true)
              public $value =>
              string(17) "Surrey Quays Road"
              public $updated =>
              string(0) ""
            }
            'city' =>
            class DrawMyAttention\PHPAddressr\Data#6 (3) {
              public $required =>
              bool(true)
              public $value =>
              string(6) "London"
              public $updated =>
              string(0) ""
            }
            'state' =>
            class DrawMyAttention\PHPAddressr\Data#7 (3) {
              public $required =>
              bool(false)
              public $value =>
              string(14) "Greater London"
              public $updated =>
              string(0) ""
            }
            'postcode' =>
            class DrawMyAttention\PHPAddressr\Data#11 (3) {
              public $required =>
              bool(true)
              public $value =>
              string(0) ""
              public $updated =>
              string(0) ""
            }
            'country' =>
            class DrawMyAttention\PHPAddressr\Data#8 (3) {
              public $required =>
              bool(true)
              public $value =>
              string(14) "United Kingdom"
              public $updated =>
              string(0) ""
            }
          }
          private $longitude =>
          double(-0.051054)
          private $latitude =>
          double(51.4967696)
    */
    
## Contributing

Please submit any contributions via a pull request. Any submissions should be backed by tests in order to be merged.

## Licence

This project is open-sourced software licenced under the [MIT license](http://opensource.org/licenses/MIT).