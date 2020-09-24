<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "db.php";

if (
    isset($_GET['get_accounts'])
    && isset($_GET['user'])
) {

    $user = $_GET['user'];

    $query = "SELECT *
                FROM processing_accounts AS pa
               WHERE pa.user=$user";
    $result = queryToDataBase($query);
    $result = json_encode($result);

    echo $result;

} else if (
    isset($_GET['get_cards'])
    && isset($_GET['login'])
    && isset($_GET['password'])
) {

    $settings = optionsFromDataBase()[0];

    $url = $settings['processing_url'];
    $program = $settings["processing_program"];
    $payform = $settings["form_instant"];
    $file = "direct.py";
    $transact = getExtTransact();
    $program_sign = md5( $settings['processing_skeys'] . $transact);

    $login = utf8_encode($_GET['login']);
    if ($login[0] == "+" || $login[0] == " ")
        $login = substr($login, 1);

    $password = encryptPassword($_GET['password'], $transact);
    $params = "ext_transact=$transact&program_sign=$program_sign&program=$program&cabinet_login=$login&dkcp_protocol_version=LAST&lang=ru&password=$password&cmd=get_form_fields&payform=$payform";
    $params .= "&login=$login";
    $request = "$url/$file?$params";

    writeLogs("Send request $url");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $receiveXML = curl_exec($curl);
    curl_close($curl);

    writeLogs("Response from processing " . $receiveXML);

    try {
        $xml = new SimpleXMLElement($receiveXML);
        foreach ($xml->table->colvalues as $element) {
            $cards = $element->type_text_val;
            $tokens = $element->type_num_val;

            if (!empty($cards))
                $cards = explode("|", $cards);
            if (!empty($tokens))
                $tokens = explode("|", $tokens);
        }
        $result = json_encode(array("cards" => $cards, "tokens" => $tokens));

        writeLogs("Return " . $result . "\n____________________");
    } catch (Exception $e) {
        writeLogs("Error  " . $e->getMessage() . "\n____________________");
    }

    return empty($result['cards']) ? json_encode("Empty") : $result;

}
