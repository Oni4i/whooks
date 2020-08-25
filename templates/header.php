<?php
session_start();
ob_start();
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/const.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/auth.php";
//require_once $_SERVER["DOCUMENT_ROOT"] . "/include/dkcp.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
