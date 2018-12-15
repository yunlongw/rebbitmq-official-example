<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

/**
 * 将信息发布到交换机中
 */
$channel->exchange_declare('logs', 'fanout', false, false, false);

$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "info: Hello World! No:";
//$msg = new AMQPMessage($data);
//$msg = new AMQPMessage($data,
//    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
//);


for ($i = 0; $i < 10; $i++) {
    $channel->basic_publish((new AMQPMessage($data . $i, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT))), 'logs');
//    $channel->basic_publish($msg, 'logs');
    echo " [x] Sent ", $data . $i, "\n";
}



$channel->close();
$connection->close();
