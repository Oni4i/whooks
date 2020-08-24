<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";


if (isset($_GET['get_wallets'])) {
    $query = "select wallets.code, wallet_phone,
                wallet_token, wallet_token_valid_date,
                processing_accounts.login, card_token
                from wallets, processing_accounts
                where wallets.processing_account = processing_accounts.code";

    writeLogs("Отправляю запрос на получение wallets...");

    $result = queryToDataBase($query);

    writeLogs("Получен ответ...");

    $result = json_encode($result);

    writeLogs("Возвращаю " . $result . "\n____________________");

    echo $result;

} else if (isset($_GET['delete_wallet']) && isset($_GET['id']) && isset($_GET['wallet'])) {

    $wallet = $_GET['wallet'];
    $id = $_GET['id'];

    writeLogs("Отправляю запрос на получение hook_id...");

    $query = "select hook_id from wallets where code=$id";

    $result = queryToDataBase($query);

   // $result = json_encode($result);

    $hookId = $result[0]['hook_id'];

    writeLogs("Получен hook_id " . $hookId);

    $url = urlencode($hookId);
    writeLogs("Отправляю запрос на удаление WebHook..." . $url);

    $ch = curl_init('https://edge.qiwi.com/payment-notifier/v1/hooks/' . $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . utf8_decode($wallet),
        'ContentType: application/json; charset=UTF-8'));

    $result = curl_exec($ch);
    curl_close($ch);

    writeLogs("Получен ответ WebHook..." . $result);

    $qiwiResponse = json_decode($result, true);

    if (isset($qiwiResponse['response'])) {

        writeLogs("Успешное удаление хука");

        
    } else {

        writeLogs("Неудачное удаление хука");
    }

    echo $result;
}