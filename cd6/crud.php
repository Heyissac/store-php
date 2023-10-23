<?php
require_once("dbconn.php");
global $dbconn;

global $table_user;
global $table_office;
$table_user = "user";
$table_office = "offices";

// Función para obtener usuarios desde la base de datos
function getUsersFromDatabase() {
    global $dbconn;
    $query = $dbconn->query("SELECT usercode, name, lastname FROM user");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener oficinas desde la base de datos
function getOfficesFromDatabase() {
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
                    if ($stmt->execute([
                        'officeCode' => $officeCode,
                        'city' => $city,
                        'phone' => $phone,
                        'address1' => $address1,
                        'address2' => $address2,
                        'state' => $state,
                        'country' => $country,
                        'postalcode' => $postalcode,
                        'territory' => $territory,
                    ])) {
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

<!DOCTYPE html>
<html>
<head>
    <title>CRUD</title>
    <link rel="stylesheet" type="text/css" href="assets/css/crud.css">
</head>
<body>
    <h1>Formulario para tabla 'user'</h1>
    <form method="POST">
        <input type="text" name="user" placeholder="Usuario">
        <input type="password" name="password" placeholder="Contraseña">
        <input type="text" name="name" placeholder="Nombre">
        <input type="text" name="lastname" placeholder="Apellido">
        <button type="submit" name="create_user">Crear Usuario</button>
    </form>
    <h1>Formulario para tabla 'offices'</h1>
    <form method="POST">
        <input type="text" name="officeCode" placeholder="Código de oficina">
        <input type="text" name="city" placeholder="Ciudad">
        <input type="number" name="phone" placeholder="Celular">
        <input type="text" name="addressLine1" placeholder="Dirección 1">
        <input type="text" name "addressLine2" placeholder="Dirección 2">
        <input type="text" name="state" placeholder="Estado">
        <input type="text" name="country" placeholder="País">
        <input type="text" name="postalcode" placeholder="Código Postal">
        <input type="text" name="territory" placeholder="Territorio">
        <button type="submit" name="create_office">Crear Oficina</button>
    </form>

    <h1>Eliminar Todos los Usuarios</h1>
    <form method="GET">
        <button type="submit" name="delete_all_users">Eliminar Todos los Usuarios</button>
    </form>

    <h1>Eliminar Todos las Oficinas</h1>
    <form method="GET">
        <button type="submit" name="delete_all_offices">Eliminar Todas las Oficinas</button>
    </form>

    <h1>Eliminar Usuario por Usercode</h1>
    <form method="GET">
        <select name="delete_usercode">
            <option value="">Selecciona un usuario a eliminar</option>
            <?php
            foreach ($users as $user) {
                echo "<option value='" . $user['usercode'] . "'>" . $user['name'] . ' ' . $user['lastname'] . "</option>";
            }
            ?>
        </select>
        <button type="submit">Eliminar Usuario</button>
    </form>

    <h1>Eliminar Oficina por Código</h1>
    <form method="GET">
        <select name="delete_officecode">
            <option value="">Selecciona una oficina a eliminar</option>
            <?php
            foreach ($offices as $office) {
                echo "<option value='" . $office['officeCode'] . "'>" . $office['city'] . "</option>";
            }
            ?>
        </select>
        <button type="submit">Eliminar Oficina</button>
    </form>

    <h1>Lectura de Usuarios</h1>
    <table border="1">
        <tr>
            <th>Usercode</th>
            <th>User</th>
            <th>Name</th>
            <th>Lastname</th>
        </tr>
        <?php foreach ($users as $user) { ?>
            <tr>
                <td><?php echo $user['usercode']; ?></td>
                <td><?php echo $user['user']; ?></td>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['lastname']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <h1>Lectura de Oficinas</h1>
    <table border="1">
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
                <td><?php echo $office['officeCode']; ?></td>
                <td><?php echo $office['city']; ?></td>
                <td><?php echo $office['phone']; ?></td>
                <td><?php echo $office['addressLine1']; ?></td>
                <td><?php echo $office['addressLine2']; ?></td>
                <td><?php echo $office['state']; ?></td>
                <td><?php echo $office['country']; ?></td>
                <td><?php echo $office['postalCode']; ?></td>
                <td><?php echo $office['territory']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <h1>Actualizar Usuario</h1>
    <form method="POST">
        <select name="usercode">
            <option value="">Seleccione un usuario</option>
            <?php
            foreach ($users as $user) {
                echo "<option value='" . $user['usercode'] . "'>" . $user['name'] . " " . $user['lastname'] . "</option>";
            }
            ?>
        </select>
        <input type="text" name="user" placeholder="Nuevo Usuario">
        <input type="password" name="password" placeholder="Nueva Contraseña">
        <input type="text" name="name" placeholder="Nuevo Nombre">
        <input type="text" name="lastname" placeholder="Nuevo Apellido">
        <button type="submit" name="update_user">Actualizar Usuario</button>
    </form>

    <h1>Actualizar Oficina</h1>
    <form method="POST">
        <select name="officeCode">
            <option value="">Seleccione una oficina</option>
            <?php
            foreach ($offices as $office) {
                echo "<option value='" . $office['officeCode'] . "'>" . $office['city'] . "</option>";
            }
            ?>
        </select>
        <input type="text" name="city" placeholder="Nueva Ciudad">
        <input type="number" name="phone" placeholder="Nuevo Teléfono">
        <input type="text" name="addressLine1" placeholder="Nueva Dirección 1">
        <input type="text" name="addressLine2" placeholder="Nueva Dirección 2">
        <input type="text" name="state" placeholder="Nuevo Estado">
        <input type="text" name="country" placeholder="Nuevo País">
        <input type="text" name="postalcode" placeholder="Nuevo Código Postal">
        <input type="text" name="territory" placeholder="Nuevo Territorio">
        <button type="submit" name="update_office">Actualizar Oficina</button>
    </form>
</body>
</html>
