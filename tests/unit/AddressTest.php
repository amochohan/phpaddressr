<?php
namespace PHPAddressrTests\Unit;

use DrawMyAttention\PHPAddressr\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    private $address;

    public function setUp()
    {
        parent::setUp();
        $this->address = new Address();
    }

    public function test_it_can_set_and_get_address_properties()
    {
        $this->address->setCompany('Energy Aspects Ltd');
        $this->assertEquals('Energy Aspects Ltd', $this->address->company());

        $this->address->setBuilding('Dock Offices');
        $this->assertEquals('Dock Offices', $this->address->building());

        $this->address->setStreet('Surrey Quays Road');
        $this->assertEquals('Surrey Quays Road', $this->address->street());

        $this->address->setCity('London');
        $this->assertEquals('London', $this->address->city());

        $this->address->setState('Greater London');
        $this->assertEquals('Greater London', $this->address->state());

        $this->address->setPostcode('SE16 2XU');
        $this->assertEquals('SE16 2XU', $this->address->postcode());

        $this->address->setCountry('United Kingdom');
        $this->assertEquals('United Kingdom', $this->address->country());
    }

    /**
     * @expectedException DrawMyAttention\PHPAddressr\Exceptions\MissingCountryException
     */
    public function test_it_throws_an_exception_without_a_country()
    {
        $this->address->setBuilding('Dock Offices')
            ->setStreet('Surrey Quays Road')
            ->setCity('London')
            ->setState('Greater London')
            ->setPostcode('SE16 2XU');

        $this->address->verify();
    }

    /**
     * @expectedException DrawMyAttention\PHPAddressr\Exceptions\MissingPostcodeException
     */
    public function test_it_throws_an_exception_without_a_postcode()
    {
        $this->address->setBuilding('Dock Offices')
            ->setStreet('Surrey Quays Road')
            ->setCity('London')
            ->setState('Greater London')
            ->setCountry('United Kingdom');

        $this->address->verify();
    }

    /**
     * @expectedException DrawMyAttention\PHPAddressr\Exceptions\MissingCityException
     */
    public function test_it_throws_an_exception_without_a_city()
    {
        $this->address->setBuilding('Dock Offices')
            ->setStreet('Surrey Quays Road')
            ->setState('Greater London')
            ->setPostcode('SE16 2XU')
            ->setCountry('United Kingdom');

        $this->address->verify();
    }

    /**
     * @expectedException DrawMyAttention\PHPAddressr\Exceptions\MissingStreetException
     */
    public function test_it_throws_an_exception_without_a_street()
    {
        $this->address->setBuilding('Dock Offices')
            ->setCity('London')
            ->setState('Greater London')
            ->setPostcode('SE16 2XU')
            ->setCountry('United Kingdom');

        $this->address->verify();
    }

    public function test_it_sets_required_attributes_on_instantiation()
    {
        $this->assertFalse($this->address->isRequired('company'));
        $this->assertFalse($this->address->isRequired('building'));
        $this->assertTrue($this->address->isRequired('street'));
        $this->assertTrue($this->address->isRequired('city'));
        $this->assertFalse($this->address->isRequired('state'));
        $this->assertTrue($this->address->isRequired('postcode'));
        $this->assertTrue($this->address->isRequired('country'));
    }

    public function test_it_can_set_an_attribute_as_required()
    {
        $this->assertFalse($this->address->isRequired('building'));
        $this->address->setAttributeAsRequired('building');
        $this->assertTrue($this->address->isRequired('building'));
    }

    public function test_it_can_set_an_attribute_as_not_required()
    {
        $this->assertTrue($this->address->isRequired('postcode'));
        $this->address->setAttributeAsRequired('postcode', false);
        $this->assertFalse($this->address->isRequired('postcode'));
    }

    public function test_it_can_be_constructed_with_initial_values()
    {
        $address = new Address([
            'building' => ['value' => 'Dock Offices'],
            'street' => ['value' => 'Surrey Quays Road', 'required' => false]
        ]);

        $this->assertEquals('Dock Offices', $address->building());
        $this->assertFalse($address->isRequired('building'));

        $this->assertEquals('Surrey Quays Road', $address->street());
        $this->assertFalse($address->isRequired('street'));

        $this->assertEquals('', $address->city());
        $this->assertTrue($address->isRequired('city'));

        $this->assertEquals('', $address->state());
        $this->assertFalse($address->isRequired('state'));

        $this->assertEquals('', $address->postcode());
        $this->assertTrue($address->isRequired('postcode'));

        $this->assertEquals('', $address->country());
        $this->assertTrue($address->isRequired('country'));
    }

    public function test_it_can_be_constructed_with_initial_values_and_default_required_values()
    {
        $address = new Address([
            'building' => 'Dock Offices',
            'street' => 'Surrey Quays Road'
        ]);

        $this->assertEquals('Dock Offices', $address->building());
        $this->assertFalse($address->isRequired('building'));

        $this->assertEquals('Surrey Quays Road', $address->street());
        $this->assertTrue($address->isRequired('street'));

        $this->assertEquals('', $address->city());
        $this->assertTrue($address->isRequired('city'));

        $this->assertEquals('', $address->state());
        $this->assertFalse($address->isRequired('state'));

        $this->assertEquals('', $address->postcode());
        $this->assertTrue($address->isRequired('postcode'));

        $this->assertEquals('', $address->country());
        $this->assertTrue($address->isRequired('country'));
    }

    public function test_it_returns_all_original_values_as_array()
    {
        $this->address->setCompany('Energy Aspects Ltd.')
            ->setBuilding('Dock Offices')
            ->setCity('London')
            ->setPostcode('SE16 2XU')
            ->setCountry('United Kingdom');

        $this->assertContains('Energy Aspects Ltd.', $this->address->toArray()['company']);
        $this->assertContains('Dock Offices', $this->address->toArray()['building']);
        $this->assertContains('London', $this->address->toArray()['city']);
        $this->assertContains('SE16 2XU', $this->address->toArray()['postcode']);
        $this->assertContains('United Kingdom', $this->address->toArray()['country']);
    }

}


