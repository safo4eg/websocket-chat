<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    require_once('../classes/DataBase.php');
    $db = new DataBase('chat', 'root', '', 'chat');
    $result = $db->loginUser('test12', '123');
    echo var_dump($result);
