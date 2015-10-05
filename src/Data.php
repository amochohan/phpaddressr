<?php
namespace DrawMyAttention\PHPAddressr;

class Data
{
    public $required = false;
    public $value = '';
    public $updated = '';

    public function __construct($value = '', $required = false)
    {
        $this->value = $value;
        $this->required = $required;
    }
}