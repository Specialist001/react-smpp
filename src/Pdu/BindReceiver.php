<?php

namespace specialist\React\Smpp\Pdu;

class BindReceiver extends Bind
{
    public function getCommandId(): int
    {
        return 0x00000001;
    }
}
