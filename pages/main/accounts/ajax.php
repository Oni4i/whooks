<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

if (isset($_GET['get_accounts_processing'])) {

    $query = "select code, uid, name, login, keyt from processing_accounts";
    $accounts = queryToDataBase($query);
    $accounts = json_encode($accounts);

    echo $accounts;
} else if (
    isset($_GET['check_account'])
    && isset($_GET['login'])
    && isset($_GET['password'])
) {



} else if (
    isset($_GET['save_account'])
    && isset($_GET['uid'])
    && isset($_GET['name'])
    && isset($_GET['login'])
    && isset($_GET['password'])
    && isset($_GET['keyt'])
) {

    $uid = $_GET['uid'];
    $name = $_GET['name'];
    $login = $_GET['login'];
    $password = $_GET['password'];
    $keyt = $_GET['keyt'];

    $query = "insert 
              high_priority ignore
              into
              processing_accounts 
              set uid = '$uid', 
              name = '$name', 
              login = '$login',
              password = $password, 
              keyt = '$keyt'
              on duplicate key update 
              name = '$name',
              login = '$login',
              password = $password, 
              keyt = '$keyt'";
    $isSuccessAccount = insertToDataBase($query);

    if ($isSuccessAccount) {
        $responseAjax = '200';
        writeLogs("New account was created");
    }
    else {
        $responseAjax = '1';
        writeLogs("New account wasn't created");
    }

    $returnResponse = array("response" => $responseAjax);
    echo json_encode($returnResponse);
}