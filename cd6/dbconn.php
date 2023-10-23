<?php

$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "dbshop";

try {
    $dbconn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if (!function_exists('getUsersFromDatabase')) {
    function getUsersFromDatabase() {
        global $dbconn;
        $table_user = "user";

        $query = "SELECT * FROM $table_user";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}

if (!function_exists('getOfficesFromDatabase')) {
    function getOfficesFromDatabase() {
        global $dbconn;
        $table_office = "offices";

        $query = "SELECT * FROM $table_office";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}
?>
