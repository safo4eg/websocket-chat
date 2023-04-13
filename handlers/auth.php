<?php
    include_once '../includes/start.php';
    require_once('../classes/DataBase.php');

    if(!empty($_POST)) {
        $db = new DataBase('chat', 'root', '', 'chat');
        $response = [];

        if(in_array('', $_POST)) {
            http_response_code(400);
            $response['errors'] = 'Все поля должны быть заполнены!';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            die();
        }

        if($_POST['action'] === 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $isSuccess = $db->loginUser($username, $password);

            if($isSuccess['status'] === 'error') {
                http_response_code(400);
                $response['errors'] = $isSuccess['errors'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }

            $_SESSION['auth']['user'] = $isSuccess['user'];
            echo json_encode($isSuccess['user'], JSON_UNESCAPED_UNICODE);
        } else if($_POST['action'] === 'register') {

        }
    }

