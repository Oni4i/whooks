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

    $responseAjax = '200';

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

    writeLogs("Получен ответ от QIWI..." . $result);

    $qiwiResponse = json_decode($result, true);

    if (isset($qiwiResponse['response'])) {

        writeLogs("Успешное удаление хука");

        writeLogs("Отправляю запрос на удаление записи хука из базы данных...");
        $query = "delete from wallets where code=$id";
        $result = insertToDataBase($query);

        if ($result) {

            writeLogs("Удаление прошло успешно");

        } else {

            writeLogs("Удаление кода $id не произведено. Необходимо удалить самостоятельно");
            $responseAjax = '2';
        }

    } else {

        writeLogs("Неудачное удаление хука");
        $responseAjax = '1';
    }

    writeLogs("Возвращаю $responseAjax");

    echo json_encode(array("response"=>$responseAjax));

} else if (isset($_GET['get_income_webhooks'])) {

    $query = "select inc, hook_date, hook_sum, hook_personId, hook_sum, dkcp_result, hook_txnId, dkcp_result_text from income_webhooks
            where next_operation LIKE '%_error'";

    writeLogs("Отправляю запрос на получение income_webhooks..." . $query);

    $result = queryToDataBase($query);

    writeLogs("Получен ответ...");

    $result = json_encode($result);

    writeLogs("Возвращаю " . $result . "\n____________________");

    echo $result;
} else if (isset($_GET['repeat_operation']) && isset($_GET['id'])) {

    writeLogs("Получен запрос repeat_operation...");

    $code = $_GET['id'];

    writeLogs("Отправляю запрос на получения hook_txnId по коду $code...");

    $query = "select hook_txnId from income_webhooks where inc=$code limit 1";
    $result = queryToDataBase($query);

    $responseAjax = '200';

    writeLogs("Получен ответ от базы данных...");

    if ($txnId = $result[0]['hook_txnId']) {

        writeLogs("Отправляю запрос на изменения next_operation кода $code...");

        $query = "update income_webhooks set next_operation='repeat' where inc=$code";
        $result = insertToDataBase($query);

        if ($result) {

            writeLogs("Изменение на repeat прошло успешно");

        } else {

            writeLogs("Изменение на repeat прошло неудачно");

            $responseAjax = '2';
        }

        writeLogs("Отправляю запрос на скрипт с txnId $txnId...");

        $url = "https://gate-dev.paypoint.pro/systems/qiwi_web_hook/repeater.php?hook_txnId=" . urlencode($txnId);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $request = curl_exec($ch);
        curl_close($ch);

        writeLogs("Запрос отправлен на $url отправлен...\n\n");

    } else {

        writeLogs("В ответе не найден hook_txnId...");

        $responseAjax = '1';
    }

    echo json_encode(array("response"=>$responseAjax));

} else if (isset($_GET['archive']) && isset($_GET['id'])) {

    $responseAjax = '200';
    $inc = $_GET['id'];
    writeLogs("Получен запрос archive...");

    $query = "insert income_webhooks_archive 
    (
    select * 
    from income_webhooks 
    where inc = $inc
    )";

    writeLogs("Отправляю запрос на вставку $query...");

    $result = insertToDataBase($query);

    if ($result) {

        writeLogs("Вставка в архив прошла успешно");

        $query = "delete from income_webhooks where inc=$inc";
        $resultDelete = insertToDataBase($query);

        if ($resultDelete) {

            writeLogs("Удаление прошло успешно\n\n");

        } else {

            writeLogs("Удаление записи не удалось\n\n");
            $responseAjax = '2';
        }

    } else {

        writeLogs("Вставка в архив не удалась \n\n");
        $responseAjax = '1';
    }

    echo json_encode(array("response" => $responseAjax));
}
