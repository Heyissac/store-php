<?php
require_once("dbconn.php");
global $dbconn;

if (isset($_POST['register'])) {
    if (isset($_POST['name']) && isset($_POST['lastname']) && isset($_POST['username']) && isset($_POST['password'])) {
        $name = trim($_POST['name']);
        $lastname = trim($_POST['lastname']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($name) && !empty($lastname) && !empty($username) && !empty($password)) {
            $verifyuser = $dbconn->prepare("select user from user where user = :user");
            $verifyuser->execute(['user' => $username]);
            $selectedUser = $verifyuser->fetch(PDO::FETCH_ASSOC);

            if ($username == $selectedUser['user']) {
                echo "Usuario existente.";
            } else {
                $sql = "insert into user (user, password, name, lastname) values (:user, :password, :name, :lastname)";
                $stmt = $dbconn->prepare($sql);

                $result = $stmt->execute(array(':user' => $username, ':password' => $password, ':name' => $name, ':lastname' => $lastname));
                if ($result) {
                    session_start();
                    $_SESSION['user'] = $username;
                    header("Location: main_index.php");
                } else {
                    echo '<br>';
                    echo 'Llena todos los campos';
                }
            }
        } else {
            echo 'Llena los campos';
        }
    }
}

if(isset($_POST['login'])){
    header('Location: login.php');
}
?>


<html !DOCTYPE>

<head>
    <title>Login</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="name" placeholder="Nombre"></input>
        <input type="text" name="lastname" placeholder="Apellido"></input>
        <input type="text" name="username" placeholder="usuario"></input>
        <input type="password" name="password" placeholder="contraseña"></input>
        <button type="submit" name="register">Registrar</button>
        <button type="submit" name="login">Login si tienes acc...</button>

    </form>
</body>

</html>