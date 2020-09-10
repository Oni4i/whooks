<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

if (isset($_GET['get_income_webhooks']) && isset($_GET['user'])) {

    $user = $_GET['user'];
    $query = "SELECT inc, hook_date, hook_sum, hook_personId, account_balance, next_operation, hook_txnId, dkcp_result_text FROM income_webhooks
              WHERE next_operation  != 'dkcp_ok'
              AND user = $user";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;

} else if (isset($_GET['repeat_operation']) && isset($_GET['id'])) {

    $code = $_GET['id'];
    $query = "SELECT hook_txnId
              FROM income_webhooks
              WHERE inc=$code
              LIMIT 1";
    $result = queryToDataBase($query);

    $responseAjax = '200';

    if ($txnId = $result[0]['hook_txnId']) {

        $query = "UPDATE income_webhooks 
                  SET next_operation='repeat' 
                  WHERE inc=$code";
        $result = insertToDataBase($query);

        if (!$result)
            $responseAjax = '2';

        $url = URL_FOR_REPEAT . "hook_txnId=" . urlencode($txnId);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $request = curl_exec($ch);
        curl_close($ch);

        writeLogs("Send request $url \n\n");

    } else {
        $responseAjax = '1';
    }

    echo json_encode(array("response"=>$responseAjax));

} else if (isset($_GET['archive']) && isset($_GET['id'])) {

    $responseAjax = '200';
    $inc = $_GET['id'];
    $query = "INSERT high_priority ignore INTO income_webhooks_archive 
              (SELECT * 
              FROM income_webhooks 
              WHERE inc = $inc)";
    $result = insertToDataBase($query);

    if ($result) {

        $query = "DELETE FROM income_webhooks 
                  WHERE inc=$inc";
        $resultDelete = insertToDataBase($query);

        if (!$resultDelete)
            $responseAjax = '2';

    } else {

        $responseAjax = '1';
    }

    echo json_encode(array("response" => $responseAjax));
}