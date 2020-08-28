<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

if (isset($_GET['get_income_webhooks'])) {

    $query = "select inc, hook_date, hook_sum, hook_personId, account_balance, next_operation, hook_txnId, dkcp_result_text from income_webhooks
            where next_operation  != 'dkcp_ok'";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;

} else if (isset($_GET['repeat_operation']) && isset($_GET['id'])) {

    $code = $_GET['id'];
    $query = "select hook_txnId from income_webhooks where inc=$code limit 1";
    $result = queryToDataBase($query);

    $responseAjax = '200';

    if ($txnId = $result[0]['hook_txnId']) {

        $query = "update income_webhooks set next_operation='repeat' where inc=$code";
        $result = insertToDataBase($query);

        if (!$result)
            $responseAjax = '2';

        $url = URLFORREPEAT . "hook_txnId=" . urlencode($txnId);
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
    $query = "insert income_webhooks_archive 
    (
    select * 
    from income_webhooks 
    where inc = $inc
    )";
    $result = insertToDataBase($query);

    if ($result) {

        $query = "delete from income_webhooks where inc=$inc";
        $resultDelete = insertToDataBase($query);

        if (!$resultDelete)
            $responseAjax = '2';

    } else {

        $responseAjax = '1';
    }

    echo json_encode(array("response" => $responseAjax));
}