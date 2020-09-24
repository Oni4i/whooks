<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

if (
    isset($_GET['get_wallets'])
    && isset($_GET['user'])
) {

    $user = $_GET['user'];

    $query = "SELECT w.code, wallet_phone, wallet_token, wallet_token_valid_date, pa.login, card_token
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

    //Send request for get hook ID
    $query = "SELECT w.hook_id 
                FROM wallets as w
               WHERE w.code=$id";
    $resultHookId = queryToDataBase($query);
    $hookId = $resultHookId[0]['hook_id'];
    $url = urlencode($hookId);

    writeLogs("Request for delete hook " . $url);

    //Delete hook from QIWI
    $ch = curl_init('https://edge.qiwi.com/payment-notifier/v1/hooks/' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . utf8_decode($wallet),
        'ContentType: application/json; charset=UTF-8'));
    $resultDelete = curl_exec($ch);
    curl_close($ch);

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

    echo json_encode(array("response"=>$responseAjax));

}