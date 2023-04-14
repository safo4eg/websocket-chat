<?php

require_once('classes/WebSocket.php');
require_once('classes/DataBase.php');

set_time_limit(0);

$NULL = NULL;
$connections = [];
$clients = [];
$max_connections = 3;
$master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$db = new DataBase('chat', 'root', '', 'chat');

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
                $connections[] = $connection;
                echo "\r\n".var_dump($connections)."\r\n";
            }
        } else {
            foreach($read as $client_socket) {
                $payloadJson = WebSocket::decode(socket_read($client_socket, 1024));
                $payloadObj = json_decode($payloadJson, JSON_UNESCAPED_UNICODE);
                if($payloadObj['type'] === 'connection') {
                    $userHash = $payloadObj['userHash'];
                    $result = $db->getUserByHash($userHash);
                    if($result['status'] !== 'ok') {
                        echo "\r\n".var_dump($result['errors']);
                    } else {
                        $clientInfo = $result['user'];
                        $clientInfo['dialogueId'] = $payloadObj['dialogueId'];
                        $clientInfo['connection'] = $client_socket;
                        $clients[] = $clientInfo;

                        foreach($clients as $client) {
                            socket_write($client['connection'], WebSocket::encode("{$clientInfo['username']} присоединился"));
                        }
                    }
                } else {
                    echo "\r\nno-connection\r\n";
                }
            }
        }
    }


    $read = $connections;
    $read[] = $master;
}