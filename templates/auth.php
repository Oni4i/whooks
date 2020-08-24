<?php

/*

if (!isset($_COOKIE["auth"]) && getPageFromPath(getCurrectPath()) != "cabinet") {
    header("Location: /cabinet");
} else {
    if ($_COOKIE["auth"] != "ok") {
        if (getPageFromPath(getCurrectPath()) != "cabinet") {
            header("Location: /cabinet");
        }
    } else {
        if (getPageFromPath(getCurrectPath()) == "cabinet") {
            header("Location: /cabinet/pages/main");
        }
    }
}
*/

//ACTUAL

if (getPageFromPath(getCurrectPath()) == "cabinet") {

    if (isset($_COOKIE["auth"]) && $_COOKIE["auth"] == "ok") {

        header("Location: /cabinet/pages/main");
    }
} else {

    if (isset($_COOKIE["auth"]) && $_COOKIE["auth"] == "ok") {

    } else {
        header("Location: /cabinet");
    }
}




/*
if (isset($_GET) && isset($_GET["auth"])) {
    $login = $_GET["login"];
    $password = $_GET["password"];

    if ($login != LOGIN) {

    }



    /* Processing request
    $url = "https://dkcp-dev.paypoint.pro";
    $script = "dkcp_kpk";
    $file = "direct.py";
    $transact = getExtTransact();
    $program_sign = md5("payment_with_deltamobile" . $transact);
    echo $transact . "<br><br>";

    $password = encryptPassword($password, $transact);
    echo $transact;


    $params = "ext_transact=$transact&program_sign=$program_sign&program=10042&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=15905";
    $request = "$url/$script/$file?$params";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $out = curl_exec($curl);
    curl_close($curl);
    echo $request;
    echo $out;

}
    */