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