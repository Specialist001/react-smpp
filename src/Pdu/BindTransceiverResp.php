<?php

namespace specialist\React\Smpp\Pdu;

class BindTransceiverResp extends BindResp
{
    public function getCommandId(): int
    {
        return 0x80000009;
    }
}
