<?php
require_once('Hasher.php');

class DataBase {
    private $link = null;

    public function __construct($host, $user, $password, $db) {
        $this->link = mysqli_connect($host, $user, $password, $db);
        if(mysqli_connect_errno()) {
            echo "Соединение не удалось: ".mysqli_connect_error(); die();
        }
    }

    public function getMessages($dialogueId) {
        $query = "SELECT m.message, UNIX_TIMESTAMP(m.timestamp) as timestamp, u.id, u.username, u.status_id FROM messages as m
                    INNER JOIN users as u ON u.id=m.user_id
                    WHERE m.dialogue_id=$dialogueId
                 ";

        $res = mysqli_query($this->link, $query);
        for($messageInfo = []; $row = mysqli_fetch_assoc($res); $messageInfo[] = $row);
        return $messageInfo;
    }

    public function addMessage($userId, $dialogueId, $message) {
        $timestamp = time();
        $query = "INSERT INTO messages 
                    SET user_id=$userId, dialogue_id=$dialogueId, message='$message', timestamp=FROM_UNIXTIME($timestamp)";
        $res = mysqli_query($this->link, $query);
    }

    public function getUserByHash($userHash) {
        $isErrors = ['status' => 'errors', 'errors' => []];
        $dataBaseHashByInputUserHash = Hasher::getDataBaseHashByUserHash($userHash);

        $query = "SELECT * FROM users WHERE hash='$dataBaseHashByInputUserHash'";
        $result = mysqli_query($this->link, $query);
        if(!$result) {
            $isErrors['errors']['mysqli'] = mysqli_error($this->link);
        }
        for($user = []; $row = mysqli_fetch_assoc($result); $user[] = $row);
        if(empty($user)) {
            $isErrors['errors']['auth'] = 'Ошибка авторизации, выполните вход повторно';
            return $isErrors;
        }

        if(!empty($isErrors['errors'])) {
            return $isErrors;
        }

        return ['status' => 'ok', 'user' => [
            'id' => $user[0]['id'],
            'username' => $user[0]['username'],
            'status_id' => $user[0]['status_id']
            ]
        ];
    }

    public function loginUser($username, $password) {
        $isErrors = ['status' => 'error', 'errors' => []];

        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($this->link, $query);
        if(!$result) {
            $isErrors['errors']['mysqli'] = mysqli_error($this->link);
        }

        for($user = []; $row = mysqli_fetch_assoc($result); $user[] = $row);

        if(empty($user)) {
            $isErrors['errors']['username'] = 'Неверная пара - username:password';
            return $isErrors;
        }

        $isPassword = password_verify($password, $user[0]['password']);
        if(!$isPassword) {
            $isErrors['errors']['username'] = 'Неверная пара - username:password';
            return $isErrors;
        }

        $userHash = $this->setHash($user[0]['id']);

        return ['status' => 'ok', 'user' => [
            'id' => $user[0]['id'],
            'username' => $user[0]['username'],
            'status_id' => $user[0]['status_id'],
            'userHash' => $userHash
            ]
        ];
    }

    public function registerUser($username, $password, $confirm) {
        $isErrors = ['status' => 'error', 'errors' => []];


        if($password !== $confirm) {
            $isErrors['errors']['confirm'] = 'Пароли не совпадают';
        }

        $query = "SELECT username FROM users WHERE username='$username'";
        $result = mysqli_query($this->link, $query);
        if(!$result) {
            $isErrors['errors']['mysqli'] = mysqli_error($this->link);
        }

        $user = mysqli_fetch_assoc($result);
        if(isset($user)) {
            $isErrors['errors']['username'] = 'Пользователь с таким username уже существует';
        }

        if(!empty($isErrors['errors'])) {
            return $isErrors;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users SET username='$username', password='$passwordHash', status_id=2";
        $result = mysqli_query($this->link, $query);
        if(!$result) {
            $isErrors['errors']['mysqli'] = mysqli_error($this->link);
        }

        $query = "SELECT id, username, status_id FROM users WHERE username='$username'";
        $result = mysqli_query($this->link, $query);
        if(!$result) {
            $isErrors['errors']['mysqli'] = mysqli_error($this->link);
        }
        for($user = []; $row = mysqli_fetch_assoc($result); $user[] = $row);
        $userHash = $this->setHash($user[0]['id']);

        if(!empty($isErrors['errors'])) {
            return $isErrors;
        }

        return ['status' => 'ok', 'user' => [
            'id' => $user[0]['id'],
            'username' => $user[0]['username'],
            'status_id' => $user[0]['status_id'],
            'userHash' => $userHash
            ]
        ];
    }

    private function setHash($id) {
        $hashes = Hasher::outputHashes();
        $query = "UPDATE users SET hash='{$hashes['dataBaseHash']}' WHERE id=$id";
        $result = mysqli_query($this->link, $query);
        return $hashes['userHash'];
    }
}