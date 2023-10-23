<?php
require_once("dbconn.php");
global $dbconn;

session_start();
$sessionUser = $_SESSION["user"];

global $table_user;
global $table_office;
$table_user = "user";
$table_office = "offices";

// Función para obtener usuarios desde la base de datos
function getUsersFromDatabase()
{
    global $dbconn;
    $query = $dbconn->query("SELECT usercode, name, lastname FROM user");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener oficinas desde la base de datos
function getOfficesFromDatabase()
{
    global $dbconn;
    $query = $dbconn->query("SELECT officeCode, city FROM offices");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

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
                echo "Usuario existente.";
            } else {
                // Insertar un nuevo usuario
                $sql = "insert into $table_user (user, password, name, lastname) VALUES (:user, :password, :name, :lastname)";
                $stmt = $dbconn->prepare($sql);
                if ($stmt->execute(['user' => $username, 'password' => $password, 'name' => $name, 'lastname' => $lastname])) {
                    echo "<br>Datos generados";
                } else {
                    echo '<br>Error al insertar los datos.';
                }
            }
        } else {
            echo 'Error en la consulta.';
        }
    } else {
        echo 'Faltan datos en el formulario de usuario.';
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
                    echo "Oficina existente.";
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
                        echo "<br>Datos de la oficina generados.";
                    } else {
                        echo '<br>Error al insertar los datos de la oficina.';
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    echo "Error al insertar datos de la oficina: " . $e->getMessage();
}

// Verificar si se ha enviado un parámetro para eliminar todos los usuarios
if (isset($_GET['delete_all_users'])) {
    $deleteAllUsers = $dbconn->prepare("DELETE FROM $table_user");
    if ($deleteAllUsers->execute()) {
        echo "Todos los usuarios han sido eliminados.";
    } else {
        echo "Error al eliminar todos los usuarios.";
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
            echo "Todas las oficinas han sido eliminadas.";
        } else {
            echo "Error al eliminar todas las oficinas.";
        }
    }
}

// Verificar si se ha enviado un ID para eliminar un usuario
if (isset($_GET['delete_usercode'])) {
    $deleteUserCode = $_GET['delete_usercode'];

    // Eliminar el usuario por su usercode
    $deleteUser = $dbconn->prepare("DELETE FROM $table_user WHERE usercode = :usercode");
    if ($deleteUser->execute(['usercode' => $deleteUserCode])) {
        if ($deleteUser->rowCount() > 0) {
            echo "Usuario con Usercode $deleteUserCode eliminado correctamente.";
        } else {
            echo "No se encontró un usuario con Usercode $deleteUserCode para eliminar.";
        }
    } else {
        echo "Error al eliminar el usuario con Usercode $deleteUserCode.";
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
                echo "Oficina con Código $deleteOfficeCode eliminada correctamente.";
            } else {
                echo "No se encontró una oficina con Código $deleteOfficeCode para eliminar.";
            }
        } else {
            echo "Error al eliminar la oficina con Código $deleteOfficeCode.";
        }
    }
}

// Leer todos los usuarios
$users = $dbconn->query("SELECT * FROM $table_user")->fetchAll(PDO::FETCH_ASSOC);

// Leer todas las oficinas
$offices = $dbconn->query("SELECT * FROM $table_office")->fetchAll(PDO::FETCH_ASSOC);
?>

<!----------------- HTML SECTION ----------------->
<!DOCTYPE html>
<html>

<head>
    <title>CRUD</title>
    <link rel="shortcut icon" href="assets/images/icons/crud.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/crud.css">
</head>

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
    <!----------------- Crud function ----------------->
    <div class="body-crud">
        <h1 class="form-header">Formulario para tabla 'user'</h1>
        <form class="user-form" method="POST">
            <div class="form-group">
                <label for="user">Usuario:</label>
                <input type="text" id="user" name="user" class="input-field" placeholder="Usuario">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" class="input-field" placeholder="Contraseña">
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
        <h1 class="form-header">Formulario para tabla 'offices'</h1>
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
                <input type="text" id="addressLine1" name="addressLine1" class="input-field" placeholder="Dirección 1">
            </div>
            <div class="form-group">
                <label for="addressLine2">Dirección 2:</label>
                <input type="text" id="addressLine2" name="addressLine2" class="input-field" placeholder="Dirección 2">
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
                <input type="text" id="postalcode" name="postalcode" class="input-field" placeholder="Código Postal">
            </div>
            <div class="form-group">
                <label for="territory">Territorio:</label>
                <input type="text" id="territory" name="territory" class="input-field" placeholder="Territorio">
            </div>
            <button type="submit" name="create_office" class="submit-button">Crear Oficina</button>
        </form>
        <!------------ Delete user and office ------------>
        <h1 class="form-header">Eliminar Todos los Usuarios</h1>
        <form class="delete-form" method="GET">
            <button type="submit" name="delete_all_users" class="delete-button">Eliminar Todos los Usuarios</button>
        </form>

        <h1 class="form-header">Eliminar Todas las Oficinas</h1>
        <form class="delete-form" method="GET">
            <button type="submit" name="delete_all_offices" class="delete-button">Eliminar Todas las Oficinas</button>
        </form>

        <h1 class="form-header">Eliminar Usuario por Usercode</h1>
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

        <h1 class="form-header">Eliminar Oficina por Código</h1>
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
        <!------------ Read user and office ------------>
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
        <!------------ Update user and office ------------>
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
    </div>
</body>

</html>