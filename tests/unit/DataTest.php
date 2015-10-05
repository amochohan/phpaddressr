<?php
namespace PHPAddressrTests\Unit;

use DrawMyAttention\PHPAddressr\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_be_instantiated_without_params()
    {
        $data = new Data();
        $this->assertInstanceOf('DrawMyAttention\PHPAddressr\Data', $data);
        $this->assertFalse($data->required);
        $this->assertEquals('', $data->value);
        $this->assertEquals('', $data->updated);
    }

    public function test_it_can_be_instantiated_with_an_initial_value()
    {
        $value = 'This is the initial value';
        $data = new Data($value);
        $this->assertFalse($data->required);
        $this->assertEquals($value, $data->value);
        $this->assertEquals('', $data->updated);
    }

    public function test_it_can_be_instantiated_with_an_initial_value_and_made_a_required_attribute()
    {
        $value = 'This is the initial value';

        $data = new Data($value, true);

        $this->assertTrue($data->required);
        $this->assertEquals($value, $data->value);
        $this->assertEquals('', $data->updated);
    }

}