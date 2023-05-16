<?php

require_once 'vendor/autoload.php';

use php\Request;
use php\User;

/* multi account
$sessions = Request::cookie('session');
foreach ( $sessions as $md5_login => $token ){
    $User = User::checkAuthorization($md5_login, $token);
    if ( $User->isLogged() ){
        $sessions[$md5_login] = $User->getData();
    }
}
$sessions['length'] = count($sessions);
die( json_encode($sessions, JSON_PRETTY_PRINT) );*/


$login = Request::cookie('login');
$token = Request::cookie('token');
$User = User::checkAuthorization($login, $token);
if ( $User->isLogged() ){
    die( json_encode((object)$User->getData()) );
}else{
    die( json_encode((object)['error'=>'Вы не авторизовались']) );
}
