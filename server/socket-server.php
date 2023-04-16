<?php
include_once 'includes/start.php';
require_once('classes/WebSocket.php');
require_once('classes/DataBase.php');

set_time_limit(0);

$NULL = NULL;
$connections = [];
$clients = [];
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
                $connections[] = $connection;
            }
        } else {
            foreach($read as $client_socket) {
                $frame = socket_read($client_socket, 1024);
                $frameType = WebSocket::checkPayloadType($frame);
                if($frameType === 'text') {
                    $payloadJson = WebSocket::decode($frame);
                    $payloadObj = json_decode($payloadJson, JSON_UNESCAPED_UNICODE);
                    $userHash = $payloadObj['userHash'];
                    $dialogueId = $payloadObj['dialogueId'];
                    if($payloadObj['type'] === 'connection') {
                        $result = $db->getUserByHash($userHash);
                        if($result['status'] !== 'ok') {
                            echo "\r\n".var_dump($result['errors']);
                        } else {
                            $clientInfo = $result['user'];
                            $clientInfo['dialogueId'] = $payloadObj['dialogueId'];
                            $clientInfo['hash'] = $userHash;
                            $clientInfo['connection'] = $client_socket;
                            $clients[] = $clientInfo;

                            $response = [
                                'type' => 'connection',
                                'message' => null,
                                'pastMessages' => null
                            ];

                            $response['pastMessages'] = $db->getMessages($dialogueId);
                            socket_write($client_socket, WebSocket::encode(json_encode($response, JSON_UNESCAPED_UNICODE)));
                            unset($response['pastMessages']);

                            $response['message'] = "{$clientInfo['username']} присоединился";
                            foreach($clients as $client) {
                                socket_write($client['connection'], WebSocket::encode(json_encode($response, JSON_UNESCAPED_UNICODE)));
                            }
                        }
                    } elseif($payloadObj['type'] === 'message') {
                        $message = $payloadObj['message'];

                        $response = [
                            'type' => 'message',
                            'username' => null,
                            'id' => null,
                            'message' => $message
                        ];

                        foreach($clients as $client) {
                            if($client['hash'] = $userHash) {
                                $response['username'] = $client['username'];
                                $response['id'] = $client['id'];
                            }
                        }

                        $db->addMessage($response['id'], $dialogueId, $message);

                        foreach($clients as $client) {
                            socket_write($client['connection'], WebSocket::encode(json_encode($response, JSON_UNESCAPED_UNICODE)));
                        }

                    } else {
                        echo "\r\n"."other"."\r\n";
                    }
                } elseif($frameType === 'close') {
                    $response = [
                        'type' => 'connection',
                        'message' => null
                    ];

                    $connectionKey = array_search($client_socket, $connections);
                    unset($connections[$connectionKey]);

                    foreach($clients as $key => $client) {
                        if($client['connection'] == $client_socket) {
                            $response['message'] = "{$client['username']} отключился";
                            unset($clients[$key]);
                        }
                    }

                    foreach($clients as $client) {
                        socket_write($client['connection'], WebSocket::encode(json_encode($response, JSON_UNESCAPED_UNICODE)));
                    }

                    echo "\r\n\r\n\r\n\r\n"."close"."\r\n";
                }
            }
        }
    }


    $read = $connections;
    $read[] = $master;
}