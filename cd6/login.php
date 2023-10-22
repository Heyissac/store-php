<?php
/* ========================= DB Connection ======================== */
require_once("dbconn.php");
global $dbconn;

/* ========================= Login button ========================= */
if (isset($_POST['login'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $verifyData = $dbconn->prepare("SELECT * FROM user WHERE user = :user");
            $verifyData->execute(['user' => $username]);

            if ($verifyData->rowCount() > 0) {
                $getData = $verifyData->fetch(PDO::FETCH_ASSOC);
                if ($username == $getData['user']) {
                    if ($password == $getData['password']) {
                        session_start();
                        $_SESSION['user'] = $username;
                        header("Location: main_index.php");
                    } else {
                        echo "Contraseña incorrecta";
                    }
                }
            } else {
                echo "Usuario no existente";
            }
        } else {
            echo "Llene los campos.";
        }
    }
}
/* ========================= Register button ========================= */
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