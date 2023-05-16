<?php

require_once 'vendor/autoload.php';

use php\DB;
use php\Request;

$md5_login = Request::cookie('login');
$token = Request::cookie('token');
DB::query("UPDATE `users` SET `number` = 0, `ts` = 0, `token` = '' WHERE `login` = '$md5_login' AND `token` = '$token'");

// single account logout
setcookie('login', '');
setcookie('token', '');

/* multi account logout
setcookie("session[$md5_login]", '');
unset($_COOKIE['session'][$md5_login]);
$sessions = $_COOKIE['session'];
setcookie('login', $sessions[ count($sessions)-1 ]);
setcookie('token', $sessions[ count($sessions)-1 ]);*/

header('Location: /');