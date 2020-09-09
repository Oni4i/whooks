<?php

function connectToDb() {
    $mysqli = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_NAME);

    if ($mysqli->connect_errno) {
        writeLogs("Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }

    return $mysqli;
}

function queryToDataBase($query) {
    $mysqli = connectToDb();

    writeLogs("Received $query");

    if ($result = $mysqli->query($query)) {
        $resultArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $mysqli->close();

        writeLogs("Sent result " . json_encode($resultArray));

        return $resultArray;
    }

    return null;
}

function insertToDataBase($query) {
    $mysqli = connectToDb();

    writeLogs("Received $query");

    if ($result = $mysqli->query($query) === TRUE)
        $result = true;
    else
        $result = false;

    return $result;
}

function optionsFromDataBase() {
    $query = "select * from settings";
    queryToDataBase($query);

    return queryToDataBase($query);
}

?>