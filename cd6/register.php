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

if (isset($_POST['login'])) {
    header('Location: login.php');
}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> Registro </title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Fontawesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container">
        <input type="checkbox" id="flip">
        <div class="cover">
            <div class="front">
                <img src="assets/images/register.jpg" alt="">
                <div class="text">
                    <span class="text-1">Cada amigo nuevo es <br> una nueva aventura</span>
                    <span class="text-2">Vamos a conectarnos</span>
                </div>
            </div>
        </div>
        <div class="forms">
            <div class="form-content">

                <div class="signup-form">
                    <div class="title">Registrarse</div>
                    <form method="POST">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input name="name" type="text" placeholder="Nombre" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input name="lastname" type="text" placeholder="Apellido" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input name="username" type="text" placeholder="Usuario" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" placeholder="Contraseña" required>
                            </div>
                            <div class="button input-box">
                                <input name="register" type="submit" value="Registrar">
                            </div>
                            <div class="text sign-up-text">¿Ya tienes una cuenta? <a href="login.php">Logear</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>