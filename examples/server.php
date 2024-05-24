<?php

use specialist\React\Smpp\Connection;
use specialist\React\Smpp\Pdu\BindTransmitter;
use specialist\React\Smpp\Pdu\BindTransmitterResp;
use specialist\React\Smpp\Pdu\SubmitSm;
use specialist\React\Smpp\Pdu\SubmitSmResp;
use specialist\React\Smpp\Proto\CommandStatus;
use specialist\React\Smpp\Server;
use Firehed\SimpleLogger\Stdout;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$loop = Loop::get();
$socketServer = new SocketServer('127.0.0.1:2775');
$logger = new Stdout();
$smppServer = new Server($socketServer, $logger);

$smppServer->on(Connection::class, static function (Connection $connection) use ($logger) {
    $connection->on(BindTransmitter::class, static function (BindTransmitter $pdu) use ($connection, $logger) {
        $logger->info('bind_transmitter. system_id: {systemId}, password: {password}', [
            'systemId' => $pdu->getSystemId(),
            'password' => $pdu->getPassword(),
        ]);

        $response = new BindTransmitterResp();
        $response->setCommandStatus(CommandStatus::ESME_ROK);
        $response->setSequenceNumber($pdu->getSequenceNumber());
        $connection->replyWith($response);
    });

    $connection->on(SubmitSm::class, static function (SubmitSm $pdu) use ($connection, $logger) {
        $logger->info('submit_sm. source: {source}, destination: {destination}, short_message: {shortMessage}', [
            'source' => $pdu->getSourceAddress()?->getValue(),
            'destination' => $pdu->getDestinationAddress()->getValue(),
            'shortMessage' => $pdu->getShortMessage(),
        ]);

        $response = new SubmitSmResp();
        $response->setSequenceNumber($pdu->getSequenceNumber());
        $response->setCommandStatus(CommandStatus::ESME_ROK);
        $response->setMessageId(uniqid('', true));
        $connection->replyWith($response);
    });

    $connection->on('error', static function (Throwable $e) use ($connection, $logger) {
        $logger->error($e->getMessage(), ['exception' => $e]);
        $connection->close();
    });
});

$loop->run();
