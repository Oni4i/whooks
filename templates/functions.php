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

function bindWebHook($params, $headers) {
    /*
    $url = URL_QIWI_WEBHOOK . '?';
    $request_headers = PUT_HEADERS;
    $url = URL_QIWI_WEBHOOK . '?' . implode("&", $params);

    foreach ($headers as $key => $value) {
        $request_headers[] = $key . ": " . $value;
    }
    putRequest($url, $headers);
    */
}

function registerWebHook($token) {

    $ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=https%3A%2F%2Fgate-dev.paypoint.pro%2Fsystems%2Fqiwi_web_hook%2Fcallback.php");
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;
}

function getKeyWebHook() {

}

function deleteWebHook() {

}

function updateWebHook() {

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
    $dir = $_SERVER["DOCUMENT_ROOT"] . '/cabinet/logs';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $log = date('Y-m-d H:i:s') . " " . $string;
    file_put_contents($dir . '/' . date("m.d.y") . '.txt', $log . PHP_EOL, FILE_APPEND);
}