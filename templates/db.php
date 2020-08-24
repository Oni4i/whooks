<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabinet/templates/const.php";

function connectToDb() {

    $mysqli = new mysqli(HOST, USERNAMEDB, PASSWORDDB, DBNAME);
    if ($mysqli->connect_errno) {
        echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    return $mysqli;
}

function queryToDataBase($query) {
    $mysqli = connectToDb();

    if ($result = $mysqli->query($query)) {

        $resultArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $mysqli->close();
        return $resultArray;
    }
    return null;
}

function optionsFromDataBase() {
    $query = "select * from settings";
    queryToDataBase($query);

    return queryToDataBase($query);
}