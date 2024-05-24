<?php

use specialist\React\Smpp\Client;
use specialist\React\Smpp\Connection;
use specialist\React\Smpp\Pdu\BindTransmitter;
use specialist\React\Smpp\Pdu\BindTransmitterResp;
use specialist\React\Smpp\Pdu\DeliverSm;
use specialist\React\Smpp\Pdu\DeliverSmResp;
use specialist\React\Smpp\Pdu\SubmitSm;
use specialist\React\Smpp\Pdu\SubmitSmResp;
use specialist\React\Smpp\Proto\Address;
use specialist\React\Smpp\Proto\Address\Ton;
use specialist\React\Smpp\Proto\Address\Npi;
use Firehed\SimpleLogger\Stdout;
use React\EventLoop\Loop;
use React\Socket\Connector;

require_once 'vendor/autoload.php';

$loop = Loop::get();
$connector = new Connector($loop);
$smppClient = new Client($connector, $loop);

$smppClient
    ->connect('127.0.0.1:2775')
    ->then(function (Connection $connection) {
        $logger = new Stdout();
        $logger->info('Connected');

        $connection->on(DeliverSm::class, function (DeliverSm $pdu) use ($connection) {
            $connection->replyWith(new DeliverSmResp());
        });

        $bindTransmitter = new BindTransmitter();
        $bindTransmitter->setSystemId('user');
        $bindTransmitter->setPassword('password');

        $connection
            ->send($bindTransmitter)
            ->then(function (BindTransmitterResp $pdu) use ($connection, $logger) {
                $logger->info('Bound');

                $submitSm = new SubmitSm();
                $submitSm->setSourceAddress(new Address(Ton::international(), Npi::isdn(), '1234567890'));
                $submitSm->setDestinationAddress(new Address(Ton::international(), Npi::isdn(), '1234567890'));
                $submitSm->setShortMessage('Hello there!');
                return $connection->send($submitSm);
            })
            ->then(function (SubmitSmResp $pdu) use ($connection, $logger) {
                $logger->info('Submitted. message_id: {messageId}', [
                    'messageId' => $pdu->getMessageId(),
                ]);
                $connection->close();
            })
        ;
    })
;

$loop->run();
