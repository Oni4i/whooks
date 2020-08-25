<<<<<<< HEAD
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

$settings = optionsFromDataBase()[0];

if (isset($_GET['get_accounts'])) {
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

    $params += "&login=$login";
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

    $hook_id = utf8_decode($_GET['hook_id']);
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
    $phone = $_GET['phone'];
    $wallet_token = $_GET['wallet_token'];
    $date = $_GET['date'];
    $account = $_GET['account'];
    $card_token = $_GET['card_token'];
    $hook_id = utf8_decode($_GET['hook_id']);
    $secret_key = utf8_decode($_GET['secret_key']);

    /*"insert into wallets (code, wallet_phone, wallet_token, wallet_token_valid_date, processing_account, card_token, hook_id)
values (code, '22', '231321', '10-10-2000', '1', '231321', '213')";*/
    //$query = "insert into wallets (code, wallet_phone, wallet_token, wallet_token_valid_date, processing_account, card_token, hook_id, secret_key)
    //            values ($code, '$phone', '$wallet_token', '$date', $account, '$card_token', '$hook_id', '$secret_key')";

    $query = "insert high_priority ignore into wallets set wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date',
                processing_account = $account, card_token = '$card_token', hook_id = '$hook_id', secret_key = '$secret_key'
                on duplicate key update wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date'";

    writeLogs("Запрос $query");

    $result = queryToDataBase($query);

    writeLogs($result ? "Успешная запись" : "Неудачная запись");

    $result = json_encode($result);

    echo $result;
}
/*
else if (isset($_GET['get_cards']) && isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $query = "select * from wallets
            left join processing_accounts pa on pa.code = wallets.processing_account 
            where pa.uid = $uid";

    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;
}
*/


/*
 *         $url = "https://dkcp-dev.paypoint.pro";
        $script = "dkcp_kpk";
        $file = "direct.py";
        $transact = getExtTransact();
        $program_sign = md5("I3AtT0CFEoZjZMFNkkOh" . $transact);

            $login = "kruzyabra@yandex.ru";
        $password = encryptPassword("123456", $transact);

        echo $transact . "<br></br>";


        $params = "ext_transact=$transact&program_sign=$program_sign&program=10075&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=15905";
        $request = "$url/$script/$file?$params";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($curl);
        curl_close($curl);
        echo $request;
        echo $out;
=======
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

$settings = optionsFromDataBase()[0];

if (isset($_GET['get_accounts'])) {
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

    $params += "&login=$login";
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

    $hook_id = utf8_decode($_GET['hook_id']);
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
    $phone = $_GET['phone'];
    $wallet_token = $_GET['wallet_token'];
    $date = $_GET['date'];
    $account = $_GET['account'];
    $card_token = $_GET['card_token'];
    $hook_id = utf8_decode($_GET['hook_id']);
    $secret_key = utf8_decode($_GET['secret_key']);


    

    /*"insert into wallets (code, wallet_phone, wallet_token, wallet_token_valid_date, processing_account, card_token, hook_id)
values (code, '22', '231321', '10-10-2000', '1', '231321', '213')";*/
    //$query = "insert into wallets (code, wallet_phone, wallet_token, wallet_token_valid_date, processing_account, card_token, hook_id, secret_key)
    //            values ($code, '$phone', '$wallet_token', '$date', $account, '$card_token', '$hook_id', '$secret_key')";

    $query = "insert high_priority ignore into wallets set wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date',
                processing_account = $account, card_token = '$card_token', hook_id = '$hook_id', secret_key = '$secret_key'
                on duplicate key update wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date'";

    writeLogs("Запрос $query");

    $result = queryToDataBase($query);

    writeLogs($result ? "Успешная запись" : "Неудачная запись");

    $result = json_encode($result);

    echo $result;
}
/*
else if (isset($_GET['get_cards']) && isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $query = "select * from wallets
            left join processing_accounts pa on pa.code = wallets.processing_account 
            where pa.uid = $uid";

    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;
}
*/


/*
 *         $url = "https://dkcp-dev.paypoint.pro";
        $script = "dkcp_kpk";
        $file = "direct.py";
        $transact = getExtTransact();
        $program_sign = md5("I3AtT0CFEoZjZMFNkkOh" . $transact);

            $login = "kruzyabra@yandex.ru";
        $password = encryptPassword("123456", $transact);

        echo $transact . "<br></br>";


        $params = "ext_transact=$transact&program_sign=$program_sign&program=10075&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=15905";
        $request = "$url/$script/$file?$params";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($curl);
        curl_close($curl);
        echo $request;
        echo $out;
>>>>>>> c2f0c89dd48285022f59a004f8243661393bf408
 */