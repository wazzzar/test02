<?php

require_once 'vendor/autoload.php';

use php\Request;
use php\User;

if ( Request::method('post') ){
    $User = User::login( Request::get('login'), Request::get('pass') );
    header('Content-Type: application/json');
    die( json_encode((object)$User->get_data()) );
}