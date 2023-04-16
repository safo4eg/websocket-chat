<?php
    require_once('../classes/DataBase.php');
    include_once '../includes/start.php';
    if(!empty($_POST)) {
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

            echo json_encode($isSuccess['user'], JSON_UNESCAPED_UNICODE); die();
        } else if($_POST['action'] === 'register') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];
            $isSuccess = $db->registerUser($username, $password, $confirm);

            if($isSuccess['status'] === 'error') {
                http_response_code(400);
                $response['errors'] = $isSuccess['errors'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }

            echo json_encode($isSuccess['user'], JSON_UNESCAPED_UNICODE); die();
        }
    }

