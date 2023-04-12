<?php

require_once('classes/WebSocket.php');

set_time_limit(0);

$NULL = NULL;
$connections = [];
$max_connections = 3;
$master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (!socket_bind($master, '127.0.0.1', 4545)) {
    echo 'Сокет не привязан. Причина: ' . socket_strerror(socket_last_error());
}

if (!socket_listen($master)) {
    echo 'Сокет не прослушивается. Причина: ' . socket_strerror(socket_last_error());
}

$read = array($master);

echo 'Ok. Socket created!';

while (true) {

    $num_changed = socket_select($read, $NULL, $NULL, 0, 10);

    if ($num_changed) {
        if (in_array($master, $read)) {
            if (count($connections) < $max_connections) {
                $connection = socket_accept($master);
                $headers = socket_read($connection, 1024);
                $responseHeader = WebSocket::createResponseHeaders($headers);
                socket_write($connection, $responseHeader);

//                foreach ($connections as $c) {
//                    socket_write($c, WebSocket::encode('New user in our chat!'));
//                }

                $connections[] = $connection;
            }
        } else {
            $messages = null;
            $message = WebSocket::decode(socket_read(end($read), 1024));
            foreach ($connections as $c) {
                socket_write($c, WebSocket::encode($message));
            }
        }
    }

    $read = $connections;
    $read[] = $master;
}