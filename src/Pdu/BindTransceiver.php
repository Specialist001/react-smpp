<?php

namespace specialist\React\Smpp\Pdu;

class BindTransceiver extends Bind
{
    public function getCommandId(): int
    {
        return 0x00000009;
    }
}
