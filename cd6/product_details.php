<?php
require_once("dbconn.php");
global $dbconn;
session_start();
$sessionUser = $_SESSION['user'];

// Realiza una consulta para recuperar el nombre del producto y la línea de producto, ordenados por nombre
$query = "SELECT p.productName, pl.productLine
          FROM products p
          INNER JOIN productlines pl ON p.productLine = pl.productLine
          ORDER BY p.productName";

$result = $dbconn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="assets/images/icons/products.png" type="image/x-icon">
    <title>Productos con Línea de Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/product.css">
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
                    <li><a href="crud.php"><i class="bi fs-5 bi-cloud-fog-fill me-2" style="padding: 10px"></i>CRUD</a>
                    </li>
                    <li><a href="order_details.php"><i class="bi fs-5 bi-list-ul me-2"
                                style="padding: 10px"></i>Ordenes</a></li>
                    <li><a href="form.php"><i class="bi fs-5 bi bi-search me-2" style="padding: 10px"></i>Buscar
                            tablas</a></li>
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
    <!------------ Data section ------------->
    <div class="body-toshow-data">
        <div class="container">
            <h1>Productos con Línea de Producto (Ordenados por Nombre)</h1>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Nombre del Producto</th>
                        <th>Línea de Producto</th>
                    </tr>
                    <?php
                    // Recorre los resultados de la consulta y muestra la línea de producto y el nombre del producto en la tabla
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['productName'] . "</td>";
                        echo "<td>" . $row['productLine'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>