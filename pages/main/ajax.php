<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

$settings = optionsFromDataBase()[0];

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

    $query = "select inc, hook_date, hook_sum, hook_personId, account_balance, next_operation, hook_txnId, dkcp_result_text from income_webhooks
            where next_operation  != 'dkcp_ok'";

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

        $url = URLFORREPEAT . "hook_txnId=" . urlencode($txnId);
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

} else if (isset($_GET['get_suc_webhooks']) && isset($_GET['page'])) {

    $page = $_GET['page'];
    $skip = ($page - 1) * 50;
    $numberOfRows = 50;

    $skip = $page == '1' ? $page : $page + 1;

    $query = "select inc, hook_date, hook_sum, hook_personId, account_balance, next_operation, hook_txnId, dkcp_result_text from income_webhooks_archive
            where next_operation='dkcp_ok' limit $numberOfRows";

    writeLogs("Отправляю запрос на получение успешных webhook из архива..." . $query);

    $result = queryToDataBase($query);

    writeLogs("Получен ответ...");

    $result = json_encode($result);

    writeLogs("Возвращаю " . $result . "\n____________________");

    echo $result;
} else if (isset($_GET['get_accounts'])) {

    $query = "select * from processing_accounts";

    writeLogs("Отправляю запрос на получение аккаунтов...");

    $result = queryToDataBase($query);

    writeLogs("Получен ответ...");

    $result = json_encode($result);

    writeLogs("Возвращаю " . $result . "\n____________________");

    echo $result;

} else if (isset($_GET['get_cards']) && isset($_GET['login']) && isset($_GET['password'])) {

    $url = $settings['processing_url'];
    $program = $settings["processing_program"];
    $payform = $settings["form_instant"];
    $file = "direct.py";
    $transact = getExtTransact();
    $program_sign = md5( $settings['processing_skeys'] . $transact); //

    $login = utf8_encode($_GET['login']);
    if ($login[0] == "+" || $login[0] == " ") {
        $login = substr($login, 1);
    }

    $password = encryptPassword($_GET['password'], $transact);
    $params = "ext_transact=$transact&program_sign=$program_sign&program=$program&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=$payform";

    $params .= "&login=$login";
    $request = "$url/$file?$params";

    writeLogs("Отправляю запрос на получение карт " . $request);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $out = curl_exec($curl);
    curl_close($curl);

    writeLogs("Получен ответ от процессинга " . $out);
    try {
        $xml = new SimpleXMLElement($out);
        foreach ($xml->table->colvalues as $element) {
            $cards = $element->type_text_val;
            if (!empty($cards)) {

                $cards = explode("|", $cards);
            }

            $tokens = $element->type_num_val;

            if (!empty($tokens)) {

                $tokens = explode("|", $tokens);
            }
        }

        $result = json_encode(array("cards" => $cards, "tokens" => $tokens));
        writeLogs("Возвращаю  " . $result . "\n____________________");
    } catch (Exception $e) {

        writeLogs("Ошибка  " . $e->getMessage() . "\n____________________");
    }

    echo empty($cards) ? json_encode("Empty") : $result;

} else if (isset($_GET['create_web_hook']) && isset($_GET['token'])) {

    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=" . urlencode($settings['notice_url']);
    $ch = curl_init($url);

    writeLogs("Отправляю запрос на создание хука $url");

    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //4b8e4a4c1d95da3236c3ea5ffb113e36 token
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_GET['token'],
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);


    writeLogs("Получен ответ от QIWI $result");
    $result = json_decode($result);
    $result = json_encode($result);
    writeLogs("Возвращаю $result \n____________________");
    echo $result;

} else if (isset($_GET['get_secret_key']) && isset($_GET['token']) && isset($_GET['hook_id'])){

    $hook_id = urldecode($_GET['hook_id']);
    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks/$hook_id/key";
    writeLogs("Отправляю запрос на получение секретного ключа $url");
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_GET['token'],
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);
    writeLogs("Получен ответ от QIWI $result");
    $result = json_decode($result);
    $result = json_encode($result);
    writeLogs("Возвращаю $result \n____________________");
    echo $result;

} else if (isset($_GET['save_web_hook']) && isset($_GET['code']) && isset($_GET['phone'])
    && isset($_GET['wallet_token']) && isset($_GET['date']) && isset($_GET['account'])
    && isset($_GET['card_token']) && isset($_GET['hook_id']) && isset($_GET['secret_key'])) {

    writeLogs("Отправляю запрос на сохранение данных в бд");

    $code = $_GET['code'];
    $phone = urldecode($_GET['phone']);
    $wallet_token = urldecode($_GET['wallet_token']);
    $date = $_GET['date'];
    $account = urldecode($_GET['account']);
    $card_token = urldecode($_GET['card_token']);
    $hook_id = urldecode($_GET['hook_id']);
    $secret_key = $_GET['secret_key'];

    writeLogs("Получен secretKey " . $_GET['secret_key']);

    $query = "insert high_priority ignore into wallets set wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date',
                processing_account = $account, card_token = '$card_token', hook_id = '$hook_id', secret_key = '$secret_key'
                on duplicate key update wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date', secret_key = '$secret_key'";

    writeLogs("Запрос $query");

    $result = insertToDataBase($query);

    writeLogs($result ? "Успешная запись" : "Неудачная запись");

    $result = json_encode($result);

    echo $result;
}
