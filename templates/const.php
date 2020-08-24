<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/config.php";

define("PUT_HEADERS", array(
    'Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
    'ContentType: application/json; charset=UTF-8'
));

define("URL_QIWI_WEBHOOK", "https://edge.qiwi.com/payment-notifier/v1/hooks");

