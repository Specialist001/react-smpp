<?php

namespace specialist\React\Smpp\Utils;

use specialist\React\Smpp\Pdu\TLV;
use specialist\React\Smpp\Proto\Address;

class DataWrapper
{
    private $position = 0;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function readInt8(): int
    {
        $value = unpack('C', $this->data, $this->position)[1];
        $this->position += 1;
        return $value;
    }

    public function writeInt8(int $value): self
    {
        $this->data .= pack('C', $value);
        return $this;
    }

    public function readInt16(): int
    {
        $value = unpack('n', $this->data, $this->position)[1];
        $this->position += 2;
        return $value;
    }

    public function readInt32(): int
    {
        $value = unpack('N', $this->data, $this->position)[1];
        $this->position += 4;
        return $value;
    }

    public function readNullTerminatedString($maxLength): string
    {
        $data = '';

        while (!$this->isEof() && !$this->isNullByte() && strlen($data) < $maxLength) {
            $data .= $this->data[$this->position];
            $this->position++;
        }
        $this->position++;

        return $data;
    }

    public function writeNullTerminatedString(string $string): self
    {
        $this->data .= $string . "\0";
        return $this;
    }

    public function writeBytes(string $string): self
    {
        $this->data .= $string;
        return $this;
    }

    public function readBytes($length): string
    {
        $data = '';
        while (!$this->isEof() && strlen($data) < $length) {
            $data .= $this->data[$this->position];
            $this->position++;
        }

        return $data;
    }

    public function readTLV(): TLV
    {
        if ($this->bytesLeft() < 4) {
            // TODO throw exception?
        }

        $tag = $this->readInt16();
        $length = $this->readInt16();
        if ($this->bytesLeft() < $length) {
            // TODO throw exception?
        }
        $value = $this->readBytes($length);
        return new TLV($tag, $value);
    }

    public function isEof()
    {
        return $this->position >= strlen($this->data);
    }

    public function isNullByte()
    {
        return $this->data[$this->position] === "\0";
    }

    public function bytesLeft()
    {
        return strlen($this->data) - $this->position;
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function readAddress($maxLength = 21): Address
    {
        $ton = new Address\Ton($this->readInt8());
        $npi = new Address\Npi($this->readInt8());
        $value = $this->readNullTerminatedString($maxLength);
        return new Address($ton, $npi, $value);
    }

    public function writeAddress(?Address $address): self
    {
        return $this
            ->writeInt8($address ? $address->getTon()->toInteger() : 0)
            ->writeInt8($address ? $address->getNpi()->toInteger() : 0)
            ->writeNullTerminatedString($address ? $address->getValue() : '')
        ;
    }
}
