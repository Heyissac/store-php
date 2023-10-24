<?php
require_once("dbconn.php");
global $dbconn;

session_start();
$sessionUser = $_SESSION["user"];

global $table_user;
global $table_office;
$table_user = "user";
$table_office = "offices";

/* ----- Función para obtener usuarios desde la base de datos ----- */
function getUsersFromDatabase()
{
    global $dbconn;
    $query = $dbconn->query("SELECT usercode, name, lastname FROM user");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/* ----- Función para obtener oficinas desde la base de datos ----- */
function getOfficesFromDatabase()
{
    global $dbconn;
    $query = $dbconn->query("SELECT officeCode, city FROM offices");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
/* ========================= Create ========================= */
if (isset($_POST['create_user'])) {
    if (isset($_POST['name']) && isset($_POST['lastname']) && isset($_POST['user']) && isset($_POST['password'])) {
        $name = trim($_POST['name']);
        $lastname = trim($_POST['lastname']);
        $username = trim($_POST['user']);
        $password = trim($_POST['password']);

        // Verificar si el usuario existe
        $verifyuser = $dbconn->prepare("SELECT user FROM $table_user WHERE user = :user");
        if ($verifyuser->execute(['user' => $username])) {
            $selectedUser = $verifyuser->fetch(PDO::FETCH_ASSOC);

            if ($selectedUser) {
                echo "<script> alert('Usuario existente'); </script>";
            } else {
                // Insertar un nuevo usuario
                $sql = "insert into $table_user (user, password, name, lastname) VALUES (:user, :password, :name, :lastname)";
                $stmt = $dbconn->prepare($sql);
                if ($stmt->execute(['user' => $username, 'password' => $password, 'name' => $name, 'lastname' => $lastname])) {
                    echo "<script> alert('Datos generados'); </script>";
                } else {
                    echo "<script> alert('Error al insertar datos'); </script>";
                }
            }
        } else {
            echo "<script> alert('Error de consulta'); </script>";
        }
    } else {
        echo "<script> alert('Faltan datos'); </script>";
    }
}

try {
    if (isset($_POST['create_office'])) {
        if (isset($_POST['officeCode']) && isset($_POST['city']) && isset($_POST['phone']) && isset($_POST['addressLine1']) && isset($_POST['addressLine2']) && isset($_POST['state']) && isset($_POST['country']) && isset($_POST['postalcode']) && isset($_POST['territory'])) {
            $officeCode = trim($_POST['officeCode']);
            $city = trim($_POST['city']);
            $phone = trim($_POST['phone']);
            $address1 = trim($_POST['addressLine1']);
            $address2 = trim($_POST['addressLine2']);
            $state = trim($_POST['state']);
            $country = trim($_POST['country']);
            $postalcode = trim($_POST['postalcode']);
            $territory = trim($_POST['territory']);

            // Verificar si la oficina ya existe (puedes usar algún otro campo único en lugar de 'officeCode' si es necesario)
            $verifyOffice = $dbconn->prepare("SELECT officeCode FROM $table_office WHERE officeCode = :officeCode");
            if ($verifyOffice->execute(['officeCode' => $officeCode])) {
                $selectedOffice = $verifyOffice->fetch(PDO::FETCH_ASSOC);

                if ($selectedOffice) {
                    echo "<script> alert('Oficina existente'); </script>";
                } else {
                    // Insertar una nueva oficina con el officeCode proporcionado
                    $sql = "INSERT INTO $table_office (officeCode, city, phone, addressLine1, addressLine2, state, country, postalcode, territory) 
                            VALUES (:officeCode, :city, :phone, :address1, :address2, :state, :country, :postalcode, :territory)";
                    $stmt = $dbconn->prepare($sql);
                    if (
                        $stmt->execute([
                            'officeCode' => $officeCode,
                            'city' => $city,
                            'phone' => $phone,
                            'address1' => $address1,
                            'address2' => $address2,
                            'state' => $state,
                            'country' => $country,
                            'postalcode' => $postalcode,
                            'territory' => $territory,
                        ])
                    ) {
                        echo "<script> alert('Datos generados'); </script>";
                    } else {
                        echo "<script> alert('Error al insertar datos'); </script>";
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    echo "Error al insertar datos de la oficina: " . $e->getMessage();
}
/* ========================= Delete ========================= */
if (isset($_GET['delete_all_users'])) {
    $deleteAllUsers = $dbconn->prepare("DELETE FROM $table_user");
    if ($deleteAllUsers->execute()) {
        echo "<script> alert('Usuarios eliminados'); </script>";
    } else {
        echo "<script> alert('No se pudo eliminar a los usuarios'); </script>";
    }
}

if (isset($_GET['delete_all_offices'])) {
    // Verificar si hay al menos una oficina con empleados relacionados
    $checkEmployees = $dbconn->query("SELECT COUNT(*) FROM employees WHERE officeCode IS NOT NULL");
    $rowCount = $checkEmployees->fetchColumn();

    if ($rowCount > 0) {
        echo "No se pueden eliminar todas las oficinas debido a que al menos una de ellas tiene empleados relacionados.";
    } else {
        // Si no hay empleados relacionados, eliminar todas las oficinas
        $deleteAllOffices = $dbconn->prepare("DELETE FROM $table_office");
        if ($deleteAllOffices->execute()) {
            echo "<script> alert('Oficinas eliminadas'); </script>";
        } else {
            echo "<script> alert('Error al eliminar las oficinas'); </script>";
        }
    }
}

if (isset($_GET['delete_usercode'])) {
    $deleteUserCode = $_GET['delete_usercode'];

    // Eliminar el usuario por su usercode
    $deleteUser = $dbconn->prepare("DELETE FROM $table_user WHERE usercode = :usercode");
    if ($deleteUser->execute(['usercode' => $deleteUserCode])) {
        if ($deleteUser->rowCount() > 0) {
            echo "<script> alert('Usuario eliminado'); </script>";
        } else {
            echo "<script> alert('Usuario no encontrado'); </script>";
        }
    } else {
        echo "<script> alert('Error al eliminar'); </script>";
    }
}

if (isset($_GET['delete_officecode'])) {
    $deleteOfficeCode = $_GET['delete_officecode'];

    // Verificar si hay empleados relacionados con esta oficina
    $checkEmployees = $dbconn->prepare("SELECT COUNT(*) FROM employees WHERE officeCode = :officeCode");
    $checkEmployees->execute(['officeCode' => $deleteOfficeCode]);
    $rowCount = $checkEmployees->fetchColumn();

    if ($rowCount > 0) {
        echo "No se puede eliminar la oficina con Código $deleteOfficeCode debido a empleados relacionados.";
    } else {
        // Eliminar la oficina por su officecode
        $deleteOffice = $dbconn->prepare("DELETE FROM $table_office WHERE officeCode = :officeCode");
        if ($deleteOffice->execute(['officeCode' => $deleteOfficeCode])) {
            if ($deleteOffice->rowCount() > 0) {
                echo "<script> alert('Oficina eliminada'); </script>";
            } else {
                echo "<script> alert('No se encontró la oficina.'); </script>";
            }
        } else {
            echo "<script> alert('Error al eliminar la oficina'); </script>";
        }
    }
}
/* ========================= Read ========================= */
$users = $dbconn->query("SELECT * FROM $table_user")->fetchAll(PDO::FETCH_ASSOC);

// Leer todas las oficinas
$offices = $dbconn->query("SELECT * FROM $table_office")->fetchAll(PDO::FETCH_ASSOC);
?>

<!----------------------- HTML SECTION ----------------------->
<!DOCTYPE html>
<html>

<head>
    <title>CRUD</title>
    <link rel="shortcut icon" href="assets/images/icons/crud.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/header.css">
    <link rel="stylesheet" type="text/css" href="assets/css/crud.css">
</head>
<header class="head">
    <div class="logo border-bottom">
        <!-- <img class="w-100" src="assets/images/geek.png" alt="" /> -->
        <a class="navbar-toggler d-block d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </a>
    </div>
    <div id="navbarNav" class="navcol d-none d-lg-block">
        <ul>
            <li><a href="#"><i class="bi bi-hand-index-fill fs-5 me-2" style="color: #3F021F"></i>Inicio</a></li>
            <li><a href="#create-form"><i class="bi fs-5 bi-file-earmark-plus-fill me-2"
                        style="color: #3F021F"></i>Crear</a>
            </li>
            <li><a href="#delete-form" name="find-table"><i class="bi fs-5 bi-file-x-fill me-2"
                        style="color: #3F021F"></i>Eliminar</a></li>
            <li><a href="#read-form" name="detail-order"><i class="bi bi-book-fill me-2"
                        style="color: #3F021F"></i>Leer</a></li>
            <li><a href="#update-form" name="detail-product"><i class="bi fs-5 bi-plus-circle-fill me-2"
                        style="color: #3F021F"></i>Actualizar</a></li>
        </ul>
    </div>
</header>

<body>
    <!------------ Navigation bar ------------>
    <nav class="nav">
        <div class="navbar-container">
            <div class="logo">
                <a href="main_index.php"><i class="bi fs-5 bi-house-fill me-2"
                        style="margin-right: 10px"></i>Geekopolis</a>
            </div>
            <div class="main_list" id="mainListDiv">
                <ul>
                    <li><a href="form.php"><i class="bi fs-5 bi bi-search me-2" style="padding: 10px"></i>Buscar
                            tablas</a></li>
                    <li><a href="order_details.php"><i class="bi fs-5 bi-list-ul me-2"
                                style="padding: 10px"></i>Ordenes</a></li>
                    <li><a href="product_details.php"><i class="bi fs-5 bi-basket-fill me-2"
                                style="padding: 10px"></i>Productos</a></li>
                    <div class="dropdown">
                        <button class="dropbtn"><i class="bi fs-5 bi-people-fill me-2"
                                style="margin-right: 10px"></i>PERFIL
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#"><i class="bi fs-5 bi-person-fill me-2" style="margin-right: 10px"></i>
                                <?php echo $sessionUser; ?>
                            </a>
                            <a href="form.php?close=1" name="close"><i class="bi fs-5 bi-x-square-fill me-2"
                                    style="margin-right: 10px"></i>Salir</a>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </nav>
    <!----------------- Crud function for user ----------------->
    <div class="main-content" name="index-crud">
        <div class="body-crud">
            <section name="create-form" id="create-form">
                <h1 class="form-header">Crear usuario</h1>
                <form class="user-form" method="POST">
                    <div class="form-group">
                        <label for="user">Usuario:</label>
                        <input type="text" id="user" name="user" class="input-field" placeholder="Usuario">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" class="input-field"
                            placeholder="Contraseña">
                    </div>
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name" class="input-field" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Apellido:</label>
                        <input type="text" id="lastname" name="lastname" class="input-field" placeholder="Apellido">
                    </div>
                    <button type="submit" name="create_user" class="submit-button">Crear Usuario</button>
                </form>
                <!----------------- Crud function for office ----------------->
                <h1 class="form-header">Crear oficina</h1>
                <form class="office-form" method="POST">
                    <div class="form-group">
                        <label for="officeCode">Código de oficina:</label>
                        <input type="text" id="officeCode" name="officeCode" class="input-field"
                            placeholder="Código de oficina">
                    </div>
                    <div class="form-group">
                        <label for="city">Ciudad:</label>
                        <input type="text" id="city" name="city" class="input-field" placeholder="Ciudad">
                    </div>
                    <div class="form-group">
                        <label for="phone">Celular:</label>
                        <input type="number" id="phone" name="phone" class="input-field" placeholder="Celular">
                    </div>
                    <div class="form-group">
                        <label for="addressLine1">Dirección 1:</label>
                        <input type="text" id="addressLine1" name="addressLine1" class="input-field"
                            placeholder="Dirección 1">
                    </div>
                    <div class="form-group">
                        <label for="addressLine2">Dirección 2:</label>
                        <input type="text" id="addressLine2" name="addressLine2" class="input-field"
                            placeholder="Dirección 2">
                    </div>
                    <div class="form-group">
                        <label for="state">Estado:</label>
                        <input type="text" id="state" name="state" class="input-field" placeholder="Estado">
                    </div>
                    <div class="form-group">
                        <label for="country">País:</label>
                        <input type="text" id="country" name="country" class="input-field" placeholder="País">
                    </div>
                    <div class="form-group">
                        <label for="postalcode">Código Postal:</label>
                        <input type="text" id="postalcode" name="postalcode" class="input-field"
                            placeholder="Código Postal">
                    </div>
                    <div class="form-group">
                        <label for="territory">Territorio:</label>
                        <input type="text" id="territory" name="territory" class="input-field" placeholder="Territorio">
                    </div>
                    <button type="submit" name="create_office" class="submit-button">Crear Oficina</button>
                </form>
            </section>
            <!------------ Delete user and office ------------>
            <section name="delete-form" id="delete-form">
                <h1 class="form-header">Eliminar Todos los Usuarios</h1>
                <form class="delete-form" method="GET">
                    <button type="submit" name="delete_all_users" class="delete-button">Eliminar Todos los
                        Usuarios</button>
                </form>

                <h1 class="form-header">Eliminar Todas las Oficinas</h1>
                <form class="delete-form" method="GET">
                    <button type="submit" name="delete_all_offices" class="delete-button">Eliminar Todas las
                        Oficinas</button>
                </form>

                <h1 class="form-header">Eliminar Usuario</h1>
                <form class="delete-form" method="GET">
                    <select name="delete_usercode" class="select-field">
                        <option value="">Selecciona un usuario a eliminar</option>
                        <?php
                        foreach ($users as $user) {
                            echo "<option value='" . $user['usercode'] . "'>" . $user['name'] . ' ' . $user['lastname'] . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="delete-button">Eliminar Usuario</button>
                </form>

                <h1 class="form-header">Eliminar Oficina</h1>
                <form class="delete-form" method="GET">
                    <select name="delete_officecode" class="select-field">
                        <option value="">Selecciona una oficina a eliminar</option>
                        <?php
                        foreach ($offices as $office) {
                            echo "<option value='" . $office['officeCode'] . "'>" . $office['city'] . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="delete-button">Eliminar Oficina</button>
                </form>
            </section>
            <!------------ Read user and office ------------>
            <section name="read-form" id="read-form">
                <h1 class="table-header">Lectura de Usuarios</h1>
                <table class="data-table">
                    <tr>
                        <th>Usercode</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>Lastname</th>
                    </tr>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td>
                                <?php echo $user['usercode']; ?>
                            </td>
                            <td>
                                <?php echo $user['user']; ?>
                            </td>
                            <td>
                                <?php echo $user['name']; ?>
                            </td>
                            <td>
                                <?php echo $user['lastname']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

                <h1 class="table-header">Lectura de Oficinas</h1>
                <table class="data-table">
                    <tr>
                        <th>OfficeCode</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>AddressLine1</th>
                        <th>AddressLine2</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>PostalCode</th>
                        <th>Territory</th>
                    </tr>
                    <?php foreach ($offices as $office) { ?>
                        <tr>
                            <td>
                                <?php echo $office['officeCode']; ?>
                            </td>
                            <td>
                                <?php echo $office['city']; ?>
                            </td>
                            <td>
                                <?php echo $office['phone']; ?>
                            </td>
                            <td>
                                <?php echo $office['addressLine1']; ?>
                            </td>
                            <td>
                                <?php echo $office['addressLine2']; ?>
                            </td>
                            <td>
                                <?php echo $office['state']; ?>
                            </td>
                            <td>
                                <?php echo $office['country']; ?>
                            </td>
                            <td>
                                <?php echo $office['postalCode']; ?>
                            </td>
                            <td>
                                <?php echo $office['territory']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </section>
            <!------------ Update user and office ------------>
            <section name="update-form" id="update-form">
                <h1 class="form-header">Actualizar Usuario</h1>
                <form class="update-form" method="POST">
                    <select name="usercode" class="select-field">
                        <option value="">Seleccione un usuario</option>
                        <?php
                        foreach ($users as $user) {
                            echo "<option value='" . $user['usercode'] . "'>" . $user['name'] . " " . $user['lastname'] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="user" class="text-input" placeholder="Nuevo Usuario">
                    <input type="password" name="password" class="text-input" placeholder="Nueva Contraseña">
                    <input type="text" name="name" class="text-input" placeholder="Nuevo Nombre">
                    <input type="text" name="lastname" class="text-input" placeholder="Nuevo Apellido">
                    <button type="submit" name="update_user" class="update-button">Actualizar Usuario</button>
                </form>

                <h1 class="form-header">Actualizar Oficina</h1>
                <form class="update-form" method="POST">
                    <select name="officeCode" class="select-field">
                        <option value="">Seleccione una oficina</option>
                        <?php
                        foreach ($offices as $office) {
                            echo "<option value='" . $office['officeCode'] . "'>" . $office['city'] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="city" class="text-input" placeholder="Nueva Ciudad">
                    <input type="number" name="phone" class="text-input" placeholder="Nuevo Teléfono">
                    <input type="text" name="addressLine1" class="text-input" placeholder="Nueva Dirección 1">
                    <input type="text" name="addressLine2" class="text-input" placeholder="Nueva Dirección 2">
                    <input type="text" name="state" class="text-input" placeholder="Nuevo Estado">
                    <input type="text" name="country" class="text-input" placeholder="Nuevo País">
                    <input type="text" name="postalcode" class="text-input" placeholder="Nuevo Código Postal">
                    <input type="text" name="territory" class="text-input" placeholder="Nuevo Territorio">
                    <button type="submit" name="update_office" class="update-button">Actualizar Oficina</button>
                </form>
            </section>
        </div>
    </div>

    <!-- <script src="assets/js/navbar.js"></script> -->
</body>

</html>