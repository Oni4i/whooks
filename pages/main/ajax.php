<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";



if (
    isset($_GET['get_wallets'])
    && isset($_GET['user'])
) {

    $resultWallets = getWallets();

    echo $resultWallets;

} else if (
    isset($_GET['delete_wallet'])
    && isset($_GET['id'])
    && isset($_GET['wallet'])
) {

    $resultDelete = deleteWallet();

    echo $resultDelete;

} else if (
    isset($_GET['get_accounts'])
    && isset($_GET['user'])
) {

    $resultAccounts = getAccounts();

    echo $resultAccounts;

} else if (
    isset($_GET['get_cards'])
    && isset($_GET['login'])
    && isset($_GET['password'])
) {

    $resultCards = getCards();

    echo $resultCards;

} else if (
    isset($_GET['create_web_hook'])
    && isset($_GET['token'])
) {

    $resultCreateHook = createWebHook();

    echo $resultCreateHook;

} else if (
    isset($_GET['get_secret_key'])
    && isset($_GET['token'])
    && isset($_GET['hook_id'])
){

    $resultSecretKey = getSecretKey();

    echo $resultSecretKey;

} else if (
    isset($_GET['save_web_hook'])
    && isset($_GET['code'])
    && isset($_GET['phone'])
    && isset($_GET['wallet_token'])
    && isset($_GET['date'])
    && isset($_GET['account'])
    && isset($_GET['card_token'])
    && isset($_GET['hook_id'])
    && isset($_GET['secret_key'])
    && isset($_GET['user'])
) {

    $resultSaveHook = saveWebHook();

    echo $resultSaveHook;
}
