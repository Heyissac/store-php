<?php
require_once("dbconn.php");
global $dbconn;

if (isset($_POST['login'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $verifyData = $dbconn->prepare("select * from user where user = :user and password = :password");
        $dataResult = $verifyData->execute(array(':user' => $username, ':password' => $password));
        //$getData = $dataResult->fetch(PDO::FETCH_ASSOC);

        if (isset($getData) && !empty($getData)) {
            session_start();
            $_SESSION['user'] = $username;
            header("Location: main_index.php");
        } else {
            echo "Usuario o contraseña incorrectos.";
        }

    }
}

if (isset($_POST['register'])) {
    header("Location: register.php");
}



?>

<!DOCTYPE html>

<head>
    <title>Login</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="username" placeholder="usuario"></input>
        <input type="password" name="password" placeholder="contraseña"></input>
        <button type="submit" name="login">Login</button>
        <button type="submit" name="register">No tienes acc...</button>

    </form>
</body>

</html>