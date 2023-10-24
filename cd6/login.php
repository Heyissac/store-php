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
                        echo "<script> alert('Contraseña incorrecta'); </script>";
                    }
                }
            } else {
                echo "<script> alert('Usuario no existente'); </script>";
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
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> Login </title>
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
                <img src="assets/images/login.png" alt="">
                <div class="text">
                    <span class="text-1">Cada amigo nuevo es <br> una nueva aventura</span>
                    <span class="text-2">Vamos a conectarnos</span>
                </div>
            </div>
            <div class="back">
                <div class="text">
                    <span class="text-1">Complete miles of journey <br> with one step</span>
                    <span class="text-2">Let's get started</span>
                </div>
            </div>
        </div>
        <div class="forms">
            <div class="form-content">
                <div class="login-form">
                    <div class="title">Login</div>
                    <form method="POST">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input name="username" type="text" placeholder="Escribe tu usuario" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" placeholder="Escribe tu contraseña" required>
                            </div>
                            <!-- <div class="text"><a href="#">Forgot password?</a></div> -->
                            <div class="button input-box">
                                <input type="submit" name="login" value="login">
                            </div>
                            <div class="text sign-up-text">¿No tienes cuenta? <a href="register.php">Crea una cuenta</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>