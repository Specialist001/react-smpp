<?php

namespace specialist\React\Smpp\Pdu;

class ReplaceSm extends Pdu
{
    public function getCommandId(): int
    {
        return 0x00000007;
    }
}
