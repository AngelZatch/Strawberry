<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$pusher = new Berrybox\Pusher;

$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', array($pusher, 'onEntry'));

$webSock = new React\Socket\Server($loop);
$webSock->listen(8080, '0.0.0.0');
$webServer = new Ratchet\Server\IoServer(
	new Ratchet\Http\HttpServer(
		new Ratchet\WebSocket\WsServer(
			new Ratchet\Wamp\WampServer(
				$pusher
			)
		)
	),
	$webSock
);

$loop->run();
