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

if (!function_exists('getProductsFromDatabase')) {
    function getProductsFromDatabase() {
        global $dbconn;
        $table_products = "products";

        $query = "SELECT * FROM $table_producs";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}

if (!function_exists('getOrdersFromDatabase')) {
    function getOrderssFromDatabase() {
        global $dbconn;
        $table_orders = "orders";

        $query = "SELECT * FROM $table_orders";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}

if (!function_exists('getCustomersFromDatabase')) {
    function getCustomersFromDatabase() {
        global $dbconn;
        $table_customers = "customers";

        $query = "SELECT * FROM $table_customers";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}

if (!function_exists('getPaymentsFromDatabase')) {
    function getPaymentsFromDatabase() {
        global $dbconn;
        $table_payments = "payments";

        $query = "SELECT * FROM $table_payments";
        $result = $dbconn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array(); // Devolver un array vacío en caso de error
        }
    }
}
?>
