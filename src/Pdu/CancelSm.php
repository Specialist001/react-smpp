<?php

namespace specialist\React\Smpp\Pdu;

class CancelSm extends Pdu
{
    public function getCommandId(): int
    {
        return 0x00000008;
    }
}
