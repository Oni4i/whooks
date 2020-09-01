<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/db.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";

$settings = optionsFromDataBase()[0];

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
}