<?php
session_start();
$sessionUser = $_SESSION['user'];

include_once("dbconn.php");
global $dbconn;

$fletchData = $dbconn->prepare("SELECT * FROM user WHERE user = :user");
$fletchData->execute(['user' => $sessionUser]);
$storeData = $fletchData->fetchAll();

if($storeData != ''){
    foreach($storeData as $row){
        echo $row['name'].'<br>';
        echo $row['lastname'].'<br>';
        echo $row['user'].'<br>';

    }
}

if (isset($_POST['exit'])) {
    session_destroy();
    header('Location: login.php');
}

if(isset($_POST['show_tables'])) {
    header('Location: form.php');
}
?>

<!DOCTYPE html>

<head>
    <title>Login</title>
</head>

<body>
    <img src="assets/images/profile.png" alt="Admin" class="rounded-circle" width="150">
    <form method="POST">
        <button type="submit" name="show_tables">Mostrar tablas</button>
        <button type="submit" name="exit">Salir</button>
    </form>
</body>

</html>