<?php

namespace specialist\React\Smpp\Pdu;

class BindReceiverResp extends BindResp
{
    public function getCommandId(): int
    {
        return 0x80000001;
    }
}
