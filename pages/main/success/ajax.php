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

    $query = "select  
              count(inc) as 'count'
              from 
              income_webhooks_archive
              where 
              (next_operation='dkcp_ok')
              and 
              (hook_date between '$dateStart 00:00:00' and '$dateEnd 23:59:59')
              and 
              (hook_sum >= $sumStart and hook_sum <= $sumEnd)
              and
              user=$user";

    if ($wallet != "all")
        $query .= " and (hook_personId = $wallet)";

    $resultCount = queryToDataBase($query);

    if ($resultCount = $resultCount[0]['count'])
        $responseAjax['count'] = $resultCount;

    $query = "select  
              inc, hook_txnId, hook_date, hook_sum, hook_personId, dkcp_sum, dkcp_transact 
              from 
              income_webhooks_archive
              where 
              (next_operation='dkcp_ok')
              and 
              (hook_date between '$dateStart 00:00:00' and '$dateEnd 23:59:59')
              and 
              (hook_sum >= $sumStart and hook_sum <= $sumEnd)
              and
              user = $user";

    if ($wallet != "all")
        $query .= " and (hook_personId = $wallet)";

    $query .= " order by inc DESC
                limit $skip, $numberOfRows";
    $result = queryToDataBase($query);
    $responseAjax['rows'] = $result;
    $responseAjax = json_encode($responseAjax);

    echo $responseAjax;

} else if (isset($_GET['generate_suc_page']) && isset($_GET['user'])) {

    $user = $_GET['user'];
    $responseAjax = array();
    $currentDate = date("yy-m-d");

    $query = "select 
              count(inc) as 'count' 
              from 
              income_webhooks_archive 
              where 
              next_operation='dkcp_ok'
              and
              (hook_date between '$currentDate 00:00:00' and '$currentDate 23:59:59')
              and 
              user = $user";
    $resultCount = queryToDataBase($query);

    if ($countOfRows = $resultCount[0]['count'])
        $responseAjax['count'] = $countOfRows;
    else
        $responseAjax['count'] = 0;

    $query = "select distinct hook_personId  from income_webhooks_archive";
    $resultWallets = queryToDataBase($query);

    if (count($resultWallets) > 0)
        $responseAjax['wallets'] = $resultWallets;
    else
        $responseAjax['wallets'] = [];

    $query = "select
              inc, hook_txnId, hook_date, hook_sum, hook_personId, dkcp_sum, dkcp_transact 
              from 
              income_webhooks_archive
              where
              (hook_date between '$currentDate 00:00:00' and '$currentDate 23:59:59')
              and 
              user = $user
              order by inc DESC
              limit 0, 50";
    $resultRows = queryToDataBase($query);

    if (count($resultRows) > 0)
        $responseAjax['rows'] = $resultRows;
    else
        $responseAjax['rows'] = [];

    $responseAjax = json_encode($responseAjax);

    echo $responseAjax;
}