<?php

$host           = "localhost";
$username       = "root";
$password       = "";
$dbname         = "dbshop";

try {
    $dbconn = new PDO("mysql:host=localhost;dbname=$dbname", $username, $password);
} catch (PDOException $e){
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

?>