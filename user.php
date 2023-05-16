<?php

require_once 'vendor/autoload.php';

use php\Request;
use php\User;

/* multi account
$sessions = Request::cookie('session');
foreach ( $sessions as $md5_login => $token ){
    $User = User::check_authorization($md5_login, $token);
    if ( $User->is_logged() ){
        $sessions[$md5_login] = $User->get_data();
    }
}
$sessions['length'] = count($sessions);
die( json_encode($sessions, JSON_PRETTY_PRINT) );*/


$login = Request::cookie('login');
$token = Request::cookie('token');
$User = User::check_authorization($login, $token);
if ( $User->is_logged() ){
    die( json_encode((object)$User->get_data()) );
}else{
    die( json_encode((object)['error'=>'Вы не авторизовались']) );
}
