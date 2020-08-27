<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/header.php";
?>

<?php


/* Create webhook
$ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks?hookType=1&txnType=2&param=https%3A%2F%2Fgate-dev.paypoint.pro%2Fsystems%2Fqiwi_web_hook%2Fcallback.php");

curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
	'ContentType: application/json; charset=UTF-8'));
$result = curl_exec($ch);
curl_close($ch);
echo $result;
*/

$ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks/163c96f3-b846-4143-b788-a71830f94b43");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
	'ContentType: application/json; charset=UTF-8'));

$result = curl_exec($ch);
curl_close($ch);
echo $result;


/* Get secret key
$hookId = ...

$ch = curl_init("https://edge.qiwi.com/payment-notifier/v1/hooks/hookId/key");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer 4b8e4a4c1d95da3236c3ea5ffb113e36',
	'ContentType: application/json; charset=UTF-8'));
$result = curl_exec($ch);
curl_close($ch);
echo $result;

 */

?>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/footer.php";
?>


