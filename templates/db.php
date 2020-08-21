<?php

function connectToDb() {
    $mysqli = new mysqli("127.0.0.1", "root", "root", "hybrid_dev");
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