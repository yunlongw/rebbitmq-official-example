<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14/014
 * Time: 16:23
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

//设置与发布程序相同；我们打开一个连接和一个通道，并声明将要消耗的队列。注意，这与发送发布的队列匹配。
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
//声明一个队伍
$channel->queue_declare('hello', false, false, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


//创建一个消费者s
//当调用 basic_consume，我们的代码会阻塞。当我们收到消息时，我们的回调函数将通过接收到返回的消息传递。
$channel->basic_consume('hello', '', false, true, false, false, function ($msg){
    echo " [x] Received ", $msg->body, "\n";
});

//循环获取队伍
while(count($channel->callbacks)) {
    $channel->wait();
}