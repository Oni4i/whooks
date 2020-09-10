<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

$settings = optionsFromDataBase()[0];

if (isset($_GET['get_accounts_processing']) && $_GET['user']) {

    $user = $_GET['user'];
    $query = "SELECT code, uid, name, login, keyt 
              FROM processing_accounts pa
              WHERE pa.user=$user";
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
    && isset($_GET['user'])
) {

    $uid = $_GET['uid'];
    $name = $_GET['name'];
    $login = $_GET['login'];
    $password = $_GET['password'];
    $keyt = $_GET['keyt'];
    $user = $_GET['user'];

    $query = "INSERT high_priority ignore
              INTO processing_accounts 
              SET uid = '$uid', 
              name = '$name', 
              login = '$login',
              password = $password, 
              keyt = '$keyt',
              user = $user
              ON duplicate KEY UPDATE
              name = '$name',
              login = '$login',
              password = $password, 
              keyt = '$keyt',
              user = $user";
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
} else if (
    isset($_GET['get_keyt'])
    && isset($_GET['login'])
    && isset($_GET['password'])
) {

    $url = $settings['processing_url'];
    $program = $settings["processing_program"];
    $file = "keyt.py";
    $transact = getExtTransact();
    $program_sign = md5( $settings['processing_skeys'] . $transact);

    $login = utf8_encode($_GET['login']);
    if ($login[0] == "+" || $login[0] == " ")
        $login = substr($login, 1);

    $password = encryptPassword($_GET['password'], $transact);
    $params = "ext_transact=$transact&program_sign=$program_sign&program=$program&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=getlist_keyt";
    $params .= "&login=$login";
    $request = "$url/$file?$params";


    writeLogs("Send request $request");

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
        $keytArray = [];
        $nameArray = [];
        foreach ($xml->table->colvalues as $element) {
            $keyt = $element->keyt;
            $name = $element->name;

            if (!empty($keyt))
                $keytArray[] = $keyt;
            if (!empty($name))
                $nameArray[] = $name;
            writeLogs($element->keyt);
        }

        $result = json_encode(array("keyt" => $keytArray, "name" => $nameArray));
        writeLogs("Return " . $result . "\n____________________");

    } catch (Exception $e) {

        writeLogs("Error  " . $e->getMessage() . "\n____________________");

    }

    echo empty($keyt) ? json_encode("Empty") : $result;
} else if (
    isset($_GET['delete_account'])
    && isset($_GET['id'])
) {

    $responseAjax = '200';
    $id = $_GET['id'];
    $query = "DELETE FROM processing_accounts
              WHERE code=$id";

    $isSuccessDelete = insertToDataBase($query);
    if (!$isSuccessDelete)
        $responseAjax = 1;

    $responseAjax = json_encode(array('response' => $responseAjax));
    echo $responseAjax;
}