<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14/014
 * Time: 16:53
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

/**
 * 消息持久化
 * 首先，我们需要确保RabbitMQ永远不会丢失队列。为了做到这一点，我们需要声明它是持久的。为此我们通过queue_declare作为第三参数为true：
 */
$channel->queue_declare('task_queue', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

/**
 * 为了改变这个分配方式，我们可以调用basic_qos方法，设置参数 prefetch_count = 1。这告诉
 * RabbitMQ不要在一个时间给一个消费者多个消息。或者，换句话说，在处理和确认以前的消息之
 * 前，不要向消费者发送新消息。相反，它将发送给下一个仍然不忙的消费者。
 */
$channel->basic_qos(null, 1, null);

$channel->basic_consume('task_queue', '', false, false, false, false, function ($msg) {
    $time = date('Y-m-d H:i:s', time());
    echo " [x]{$time} Received ", $msg->body, "\n";
    $t = rand(0, 1);
    echo "sleep time : {$t} \n";
    sleep($t);
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
});

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();