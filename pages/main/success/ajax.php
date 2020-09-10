<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

if (
    isset($_GET['get_suc_webhooks'])
    && isset($_GET['type'])
    && $_GET['type'] == 'certain'
    && isset($_GET['page'])
    && isset($_GET['date_start'])
    && isset($_GET['date_end'])
    && isset($_GET['wallet'])
    && isset($_GET['sum_start'])
    && isset($_GET['sum_end'])
    && isset($_GET['user'])
) {

    $responseAjax = array();
    $page = $_GET['page'];
    $numberOfRows = 50;
    $skip = ($page - 1) * $numberOfRows;
    $dateStart = strlen($_GET['date_start']) == 0 ? "1970-01-01" : $_GET['date_start'];
    $dateEnd = strlen($_GET['date_end']) == 0 ? "3000-01-01" : $_GET['date_end'];
    $wallet = $_GET['wallet'];
    $sumStart = strlen($_GET['sum_start']) == 0 ? 0 : $_GET['sum_start'];
    $sumEnd = strlen($_GET['sum_end']) == 0 ? PHP_INT_MAX : $_GET['sum_end'];
    $user = $_GET['user'];

    $query = "SELECT count(inc) AS 'count'
              FROM income_webhooks_archive
              WHERE (next_operation='dkcp_ok')
              AND (hook_date BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59')
              AND (hook_sum >= $sumStart AND hook_sum <= $sumEnd)
              AND user=$user";

    if ($wallet != "all")
        $query .= " and (hook_personId = $wallet)";

    $resultCount = queryToDataBase($query);

    if ($resultCount = $resultCount[0]['count'])
        $responseAjax['count'] = $resultCount;

    $query = "SELECT inc, hook_txnId, hook_date, hook_sum, hook_personId, dkcp_sum, dkcp_transact 
              FROM income_webhooks_archive
              WHERE (next_operation='dkcp_ok')
              AND (hook_date BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59')
              AND (hook_sum >= $sumStart AND hook_sum <= $sumEnd)
              AND user = $user";

    if ($wallet != "all")
        $query .= " AND (hook_personId = $wallet)";

    $query .= " ORDER BY inc DESC
                LIMIT $skip, $numberOfRows";
    $result = queryToDataBase($query);
    $responseAjax['rows'] = $result;
    $responseAjax = json_encode($responseAjax);

    echo $responseAjax;

} else if (isset($_GET['generate_suc_page']) && isset($_GET['user'])) {

    $user = $_GET['user'];
    $responseAjax = array();
    $currentDate = date("yy-m-d");

    $query = "SELECT count(inc) AS 'count' 
              FROM income_webhooks_archive 
              WHERE next_operation='dkcp_ok'
              AND (hook_date BETWEEN '$currentDate 00:00:00' AND '$currentDate 23:59:59')
              AND user = $user";
    $resultCount = queryToDataBase($query);

    if ($countOfRows = $resultCount[0]['count'])
        $responseAjax['count'] = $countOfRows;
    else
        $responseAjax['count'] = 0;

    $query = "SELECT DISTINCT hook_personId
              FROM income_webhooks_archive";
    $resultWallets = queryToDataBase($query);

    if (count($resultWallets) > 0)
        $responseAjax['wallets'] = $resultWallets;
    else
        $responseAjax['wallets'] = [];

    $query = "SELECT inc, hook_txnId, hook_date, hook_sum, hook_personId, dkcp_sum, dkcp_transact 
              FROM income_webhooks_archive
              WHERE (hook_date BETWEEN '$currentDate 00:00:00' AND '$currentDate 23:59:59')
              AND user = $user
              ORDER BY inc DESC
              LIMIT 0, 50";
    $resultRows = queryToDataBase($query);

    if (count($resultRows) > 0)
        $responseAjax['rows'] = $resultRows;
    else
        $responseAjax['rows'] = [];

    $responseAjax = json_encode($responseAjax);

    echo $responseAjax;
}