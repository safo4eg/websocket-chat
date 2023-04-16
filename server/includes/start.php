<?php

use classes\DataBase;

error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    $db = new DataBase('chat', 'root', '', 'chat');