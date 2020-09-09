<?php
require_once $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "config.php";

define("PUT_HEADERS",       array(
                                'Authorization: Bearer ' . QIWI_TOKEN,
                                'ContentType: application/json; charset=UTF-8'
                            )
);

define("URL_QIWI_WEBHOOK",  "https://edge.qiwi.com/payment-notifier/v1/hooks");

?>