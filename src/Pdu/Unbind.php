<?php

namespace specialist\React\Smpp\Pdu;

class Unbind extends Pdu
{
    public function getCommandId(): int
    {
        return 0x00000006;
    }
}
