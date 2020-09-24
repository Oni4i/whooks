<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

if (
    isset($_GET['create_web_hook'])
    && isset($_GET['token'])
) {

    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=" . urlencode($settings['notice_url']);
    $ch = curl_init($url);

    writeLogs("Send request for create hook $url");

    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_GET['token'],
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);

    writeLogs("Response from QIWI $result");

    writeLogs("Return $result \n____________________");

    echo $result;

} else if (
    isset($_GET['get_secret_key'])
    && isset($_GET['token'])
    && isset($_GET['hook_id'])
){

    $hook_id = urldecode($_GET['hook_id']);
    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks/$hook_id/key";
    $token = $_GET['token'];

    writeLogs("Send request for secret key $url");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token,
        'ContentType: application/json; charset=UTF-8'));
    $receiveQiwiAnswer = curl_exec($ch);
    curl_close($ch);

    writeLogs("Response from QIWI $receiveQiwiAnswer");

    $result = $receiveQiwiAnswer;

    writeLogs("Return $result \n____________________");

    return $result;

} else if (
    isset($_GET['save_web_hook'])
    && isset($_GET['code'])
    && isset($_GET['phone'])
    && isset($_GET['wallet_token'])
    && isset($_GET['date'])
    && isset($_GET['account'])
    && isset($_GET['card_token'])
    && isset($_GET['hook_id'])
    && isset($_GET['secret_key'])
    && isset($_GET['user'])
) {

    $code = $_GET['code'];
    $phone = urldecode($_GET['phone']);
    $wallet_token = urldecode($_GET['wallet_token']);
    $date = $_GET['date'];
    $account = urldecode($_GET['account']);
    $card_token = urldecode($_GET['card_token']);
    $hook_id = urldecode($_GET['hook_id']);
    $secret_key = $_GET['secret_key'];
    $user = $_GET['user'];

    $query = "INSERT high_priority ignore INTO wallets
                 SET wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date',
                     processing_account = $account, card_token = '$card_token', hook_id = '$hook_id', secret_key = '$secret_key', user=$user
                  ON duplicate KEY UPDATE wallet_phone = '$phone', wallet_token = '$wallet_token', 
                                          wallet_token_valid_date = '$date', secret_key = '$secret_key', user=$user";
    $result = insertToDataBase($query);
    $result = json_encode($result);

    echo $result;
}