<?php

namespace specialist\React\Smpp\Proto;

use specialist\React\Smpp\Proto\Address\Npi;
use specialist\React\Smpp\Proto\Address\Ton;

class Address
{
    /**
     * @var Ton
     */
    private $ton;

    /**
     * @var Npi
     */
    private $npi;

    /**
     * @var string
     */
    private $value;

    public function __construct(Ton $ton, Npi $npi, string $value)
    {
        $this->ton = $ton;
        $this->npi = $npi;
        $this->value = $value;
    }

    public function getTon(): Ton
    {
        return $this->ton;
    }

    public function getNpi(): Npi
    {
        return $this->npi;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
