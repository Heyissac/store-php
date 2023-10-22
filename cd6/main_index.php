<?php 
    session_start();
    $username = $_SESSION['user'];
    echo $username;

    if(isset($_POST['destroy'])){
        session_destroy();
        header('Location: login.php');
    }
?>

<!DOCTYPE html>
<head>
    <title>Login</title>
</head>

<body>
    <form method="POST">
        <button type="submit" name="destroy">Salir</button>
    </form>
</body>
</html>