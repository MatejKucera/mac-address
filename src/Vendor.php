<?php

namespace MatejKucera\MacAddress;


class Vendor
{

    public $prefix;
    public $company;
    public $address;

    public function __construct($prefix, $company, $address)
    {
        $this->prefix = $prefix;
        $this->company = $company;
        $this->address = $address;
    }
}