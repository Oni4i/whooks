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

function getWallets() {
    $user = $_GET['user'];

    $query = "SELECT w.code, wallet_phone, wallet_token, wallet_token_valid_date, pa.login, card_token
                FROM wallets AS w, processing_accounts AS pa
               WHERE w.processing_account = pa.code
                 AND w.user=$user";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    return $result;
}

function deleteWallet() {
    $wallet = $_GET['wallet'];
    $id = $_GET['id'];
    $responseAjax = '200';

    //Send request for get hook ID
    $query = "SELECT w.hook_id 
                FROM wallets as w
               WHERE w.code=$id";
    $resultHookId = queryToDataBase($query);
    $hookId = $resultHookId[0]['hook_id'];
    $url = urlencode($hookId);

    writeLogs("Request for delete hook " . $url);

    //Delete hook from QIWI
    $resultDelete = deleteHook($url, $wallet);

    writeLogs("Response from QIWI " . $resultDelete);

    $qiwiResponse = json_decode($resultDelete, true);

    if (isset($qiwiResponse['response'])) {
        writeLogs("Hook was deleted");

        //Send request for delete hook from database
        $query = "DELETE 
                    FROM wallets AS w
                   WHERE w.code=$id";
        $resultDeleteHookFromDb = insertToDataBase($query);

        if (!$resultDeleteHookFromDb)
            $responseAjax = '2';

    } else {
        $responseAjax = '1';
    }

    return json_encode(array("response"=>$responseAjax));
}

function deleteHook($url, $wallet) {

    $ch = curl_init('https://edge.qiwi.com/payment-notifier/v1/hooks/' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . utf8_decode($wallet),
        'ContentType: application/json; charset=UTF-8'));
    $resultDelete = curl_exec($ch);
    curl_close($ch);

    return $resultDelete;
}

function getAccounts() {
    $user = $_GET['user'];

    $query = "SELECT *
                FROM processing_accounts AS pa
               WHERE pa.user=$user";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    return $result;
}

function getCards() {
    $settings = optionsFromDataBase()[0];

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

    $receiveXML = sendRequestToProcessing($request);

    writeLogs("Response from processing " . $receiveXML);

    try {
        $parsedXML = getParsedXMLCardsTokens($receiveXML);

        writeLogs("Return " . $parsedXML . "\n____________________");
    } catch (Exception $e) {
        writeLogs("Error  " . $e->getMessage() . "\n____________________");
    }

    return empty($parsedXML['cards']) ? json_encode("Empty") : $parsedXML;
}

function sendRequestToProcessing($request) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $resultFromProcessing = curl_exec($curl);
    curl_close($curl);

    return $resultFromProcessing;
}

function getParsedXMLCardsTokens($XML) {
    $xml = new SimpleXMLElement($XML);
    foreach ($xml->table->colvalues as $element) {
        $cards = $element->type_text_val;
        $tokens = $element->type_num_val;

        if (!empty($cards))
            $cards = explode("|", $cards);
        if (!empty($tokens))
            $tokens = explode("|", $tokens);
    }
    $result = json_encode(array("cards" => $cards, "tokens" => $tokens));

    return $result;
}

function createWebHook() {
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

    return $result;
}

function getSecretKey() {
    $hook_id = urldecode($_GET['hook_id']);
    $url = "https://edge.qiwi.com/payment-notifier/v1/hooks/$hook_id/key";
    $token = $_GET['token'];

    writeLogs("Send request for secret key $url");

    $receiveQiwiAnswer = requestToSecretKey($url, $token);

    writeLogs("Response from QIWI $receiveQiwiAnswer");

    $result = json_decode($receiveQiwiAnswer);
    $result = json_encode($receiveQiwiAnswer);

    writeLogs("Return $result \n____________________");

    return $result;
}

function requestToSecretKey($url, $token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token,
        'ContentType: application/json; charset=UTF-8'));
    $resultFromQiwi = curl_exec($ch);
    curl_close($ch);

    return $resultFromQiwi;
}

function saveWebHook() {
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

    return $result;
}

?>