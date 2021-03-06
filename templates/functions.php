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
        $dir = PATH_FOR_LOG . "/" . date("Y-m-d");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = date('Y-m-d H:i:s') . " " . $string;
        file_put_contents($dir . '/' . LOG_NAME, $log . PHP_EOL, FILE_APPEND);
    } catch (Exception $e) {

    }
}

function getDataByLogin($login) {
    $query = "SELECT code, login, password_hash   
                FROM users
               WHERE login = '$login'
               LIMIT 1";
    $result = queryToDataBase($query);

    return !empty($result) ? $result[0] : $result;
}

function isValidUserData($data) {
    if (
        isset($data['login'])
        && isset($data['password_hash'])
        && isset($data['code'])
    ) {

        return true;
    }
    return false;
}


?>