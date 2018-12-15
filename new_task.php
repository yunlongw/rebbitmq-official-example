<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14/014
 * Time: 16:47
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

/**
 * 消息持久化
 * 首先，我们需要确保RabbitMQ永远不会丢失队列。为了做到这一点，我们需要声明它是持久的。为此我们通过queue_declare作为第三参数为true：
 */
$channel->queue_declare('task_queue', false, true, false, false);

for ($i = 0; $i < 100; $i++) {
    $msg = new AMQPMessage($i,
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
    );
    $channel->basic_publish($msg, '', 'task_queue');
}


$channel->close();
$connection->close();