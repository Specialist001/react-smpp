<?php

namespace specialist\React\Smpp\Pdu;

class TLV
{
    /**
     * @var int
     */
    private $tag;

    /**
     * @var string
     */
    private $value;

    public function __construct(int $tag, string $value)
    {
        $this->tag = $tag;
        $this->value = $value;
    }

    public function getTag(): int
    {
        return $this->tag;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
