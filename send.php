<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14/014
 * Time: 16:23
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
//获取通道
$channel = $connection->channel();

//创建一个队伍，队伍名称为 hello
$channel->queue_declare('hello', false, false, false, false);

//生产者
$channel->basic_publish((new AMQPMessage('Hello World!')), '', 'hello');
echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();