<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

$settings = optionsFromDataBase()[0];

if (
    isset($_GET['get_wallets'])
    && isset($_GET['user'])
) {

    $user = $_GET['user'];

    $query = "SELECT w.code, wallet_phone,
                     wallet_token, wallet_token_valid_date,
                     pa.login, card_token
                FROM wallets AS w, processing_accounts AS pa
               WHERE w.processing_account = pa.code
                 AND w.user=$user";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;

} else if (
    isset($_GET['delete_wallet'])
    && isset($_GET['id'])
    && isset($_GET['wallet'])
) {

    $wallet = $_GET['wallet'];
    $id = $_GET['id'];
    $responseAjax = '200';

    $query = "SELECT w.hook_id 
                FROM wallets as w
               WHERE w.code=$id";
    $result = queryToDataBase($query);

    $hookId = $result[0]['hook_id'];

    $url = urlencode($hookId);
    writeLogs("Request for delete hook " . $url);

    $ch = curl_init('https://edge.qiwi.com/payment-notifier/v1/hooks/' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . utf8_decode($wallet),
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);

    writeLogs("Response from QIWI " . $result);

    $qiwiResponse = json_decode($result, true);

    if (isset($qiwiResponse['response'])) {

        writeLogs("Hook was deleted");

        $query = "DELETE FROM wallets AS w
                   WHERE w.code=$id";
        $result = insertToDataBase($query);

        if (!$result)
            $responseAjax = '2';

    } else {

        $responseAjax = '1';

    }

    echo json_encode(array("response"=>$responseAjax));

} else if (
    isset($_GET['get_accounts'])
    && isset($_GET['user'])
) {

    $user = $_GET['user'];

    $query = "SELECT *
                FROM processing_accounts AS pa
               WHERE pa.user=$user";

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
    $program_sign = md5( $settings['processing_skeys'] . $transact);

    $login = utf8_encode($_GET['login']);
    if ($login[0] == "+" || $login[0] == " ")
        $login = substr($login, 1);

    $password = encryptPassword($_GET['password'], $transact);
    $params = "ext_transact=$transact&program_sign=$program_sign&program=$program&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=$payform";
    $params .= "&login=$login";
    $request = "$url/$file?$params";

    writeLogs("Send request $url");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $out = curl_exec($curl);
    curl_close($curl);

    writeLogs("Response from processing " . $out);
    try {
        $xml = new SimpleXMLElement($out);
        foreach ($xml->table->colvalues as $element) {
            $cards = $element->type_text_val;
            $tokens = $element->type_num_val;

            if (!empty($cards))
                $cards = explode("|", $cards);
            if (!empty($tokens))
                $tokens = explode("|", $tokens);
        }

        $result = json_encode(array("cards" => $cards, "tokens" => $tokens));
        writeLogs("Return " . $result . "\n____________________");

    } catch (Exception $e) {

        writeLogs("Error  " . $e->getMessage() . "\n____________________");

    }

    echo empty($cards) ? json_encode("Empty") : $result;

} else if (isset($_GET['create_web_hook']) && isset($_GET['token'])) {

    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=" . urlencode($settings['notice_url']);
    $ch = curl_init($url);

    writeLogs("Send request for create hook $url");

    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //4b8e4a4c1d95da3236c3ea5ffb113e36 token
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_GET['token'],
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);

    writeLogs("Response from QIWI $result");

    writeLogs("Return $result \n____________________");

    echo $result;

} else if (isset($_GET['get_secret_key']) && isset($_GET['token']) && isset($_GET['hook_id'])){

    $hook_id = urldecode($_GET['hook_id']);
    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks/$hook_id/key";

    writeLogs("Send request for secret key $url");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_GET['token'],
        'ContentType: application/json; charset=UTF-8'));
    $result = curl_exec($ch);
    curl_close($ch);

    writeLogs("Response from QIWI $result");

    $result = json_decode($result);
    $result = json_encode($result);

    writeLogs("Return $result \n____________________");

    echo $result;

} else if (isset($_GET['save_web_hook']) && isset($_GET['code']) && isset($_GET['phone'])
    && isset($_GET['wallet_token']) && isset($_GET['date']) && isset($_GET['account'])
    && isset($_GET['card_token']) && isset($_GET['hook_id']) && isset($_GET['secret_key'])
    && isset($_GET['user'])) {

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
                  ON duplicate KEY UPDATE wallet_phone = '$phone', wallet_token = '$wallet_token', wallet_token_valid_date = '$date', secret_key = '$secret_key', user=$user";
    $result = insertToDataBase($query);
    $result = json_encode($result);

    echo $result;
}
