<?php
function getExtTransact () {
    $tm = localtime(time(), 1);

    return sprintf( "%04d%02d%02d%02d%02d%02d%04d", $tm["tm_year"] + 1900, $tm["tm_mon"] + 1,
        $tm["tm_mday"], $tm["tm_hour"], $tm["tm_min"], $tm["tm_sec"], rand(1111, 9999)
    );
}

function encryptPassword($password, $transact) {
    return md5($password . $transact);
}

function getPageFromPath($url) {
    $url = explode('/', $url);
    $url =  $url[count($url)-2];

    return  $url;
}

function getCurrectPath() {
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $url = $url[0];

    return $url;
}

function writeLogs($string) {
    try {
        $dir = PATHFORLOG . "/" . date("Y-m-d");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = date('Y-m-d H:i:s') . " " . $string;
        file_put_contents($dir . '/' . LOGNAME, $log . PHP_EOL, FILE_APPEND);
    } catch (Exception $e) {

    }
}

function getDataByLogin($login) {
    $query = "
        select 
        code, login, password_hash   
        from
        users
        where
        login = '$login'
        limit 1
    ";
    $result = queryToDataBase($query);

    return $result[0];
}

function isValidUserData($data) {
    if (
        isset($data['login']) && $login = $data['login']
        && isset($data['password_hash']) && $password = $data['password_hash']
        && isset($data['code']) && $code = $data['code']
    ) {

        return true;
    }
    return false;
}



/*
 * $data = getDataByLogin($login)
 * if (isValidUserData($data) && isPasswordCorrect($ {
 *
 */