<?php

namespace php;

use php\DB;
use php\Request;

class User {

    private $data = [];
    private $authorized = 0;

    public function __construct(){

    }

    public function isLogged(): int
    {
        return $this->authorized;
    }

    public static function checkAuthorization(string $md5_login, string $token): User
    {
        $user = new User();
        if( !empty($md5_login) && !empty($token) ) {
            $res = DB::qaf("SELECT `token` FROM `users` WHERE `login` = '$md5_login'");
            $user->authorized = ($res[0]["token"] === $token? 1 : 0);
            if ($user->authorized) $user->loadData($md5_login, $token);
        }
        return $user;
    }

    public static function login(string $login, string $pass): User
    {
        $user = new User();
        if( !empty($login) && !empty($pass) ) {
            $md5_login = self::getLoginHash($login);

            $records = DB::qaf("SELECT `number`, `ts` FROM `users` WHERE `login` = '$md5_login'");
            if ( count($records) == 0 ){
                $user->data["error"] = "Нет такого пользователя";
            }else{
                $record = $records[0];
                if( (int)$record["number"] < 3 && (int)$record["ts"] == 0 ) {

                    $md5_pass = self::getPassHash($pass);
                    $_user = DB::qaf("SELECT `id`, `token` FROM `users` WHERE `login` = '$md5_login' AND `pass` = '$md5_pass'");

                    if ($_user[0]["id"]) {
                        setcookie("login", $md5_login, time() + 3600, "/");
                        if ( !empty($_user[0]["token"]) ){
                            // сохранение сессии
                            self::setToken($_user[0]["token"]);
                        }else{
                            // новая сессия
                            self::authorize($md5_login, $md5_pass);
                        }
                    }else{
                        // брутфорс счетчик
                        DB::query("UPDATE `users` SET `number` = `number`+1 WHERE `login` = '$md5_login'");
                        $user->data["error"] = "Неверный логин или пароль, у вас осталось ". (2-(int)$record["number"]) ." попыток";
                        if ( 2-(int)$record["number"] == 0 )
                            $user->data["error"] = "Попыток больше нет";
                    }
                }else{
                    // уставока времени ограничения логина
                    if ( (int)$record["ts"] == 0 ){
                        $ts = time() + 10;
                        DB::query("UPDATE `users` SET `ts` = $ts WHERE `login` = '$md5_login'");
                        $record["ts"] = $ts;
                    }

                    // проверка времени
                    if ( time() < (int)$record["ts"] ){
                        $user->data["error"] = "Вы исчерпали попытки, попробуйте снова через ". ((int)$record["ts"] - time()) ." секунд.";
                    }else{
                        // обнуление
                        DB::query("UPDATE `users` SET `number` = 0, `ts` = 0 WHERE `login` = '$md5_login'");
                        return self::login($login, $pass);
                    }
                }
            }

        }
        return $user;
    }

    public static function getLoginHash($login): string
    {
        return md5( md5($login).md5("license login") );
    }

    public static function getPassHash($pass): string
    {
        return md5( md5($pass).md5("license pass" ) );
    }

    private static function setToken($token){
        setcookie("token", $token, time()+3600, "/");
    }

    private static function authorize(string $md5_login, string $md5_pass){
        // новый токен
        $token = md5( date("c") );
        self::setToken($token);
        // multi account
        //setcookie("session[$md5_login]", $token, time()+3600, "/");

        DB::query("UPDATE `users` SET `number` = 0, `ts` = 0, `token` = '$token' WHERE `login` = '$md5_login' AND `pass` = '$md5_pass'");
        // log
        $res = DB::qaf("SELECT `fio` FROM `users` WHERE `login` = '$md5_login' AND `pass` = '$md5_pass'");
        $str = date("[Y.m.d h:i:s] ") . $res[0]["fio"] ." авторизировался\n";
        file_put_contents("access.log", $str, FILE_APPEND | LOCK_EX);
    }

    private function loadData(string $md5_login, string $token){
        $this->data = DB::qaf("SELECT `id`, `fio`, `image`, `berthday` FROM `users` WHERE `login` = '$md5_login' AND  `token` = '$token'")[0];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function info($entry){
        if( isset( $this->data[$entry] ) ) return $this->data[$entry];
        return null;
    }
}