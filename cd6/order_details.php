<?php
require_once("dbconn.php");
global $dbconn;

session_start();
$sessionUser = $_SESSION["user"];

// Realiza una consulta para recuperar los detalles de las órdenes
$query = "SELECT orderNumber, productCode, quantityOrdered, priceEach, orderLineNumber
          FROM orderdetails";

$result = $dbconn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/order.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <title>Detalles de Órdenes</title>
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
                    <li><a href="form.php"><i class="bi fs-5 bi bi-search me-2" style="padding: 10px"></i>Buscar tablas</a></li>
                    <li><a href="product_details.php"><i class="bi fs-5 bi-basket-fill me-2" style="padding: 10px"></i>Productos</a></li>
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
    <!------------ Data section ------------>
    <div class=" body-toshow-data">
        <div class="container">
        <h1>Detalles de Órdenes</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Número de Orden</th>
                            <th>Código de Producto</th>
                            <th>Cantidad Solicitada</th>
                            <th>Precio Unitario</th>
                            <th>Número de Línea de Orden</th>
                        </tr>
                    </thead>
                    <tbody>
                        <div id="result">
                            <?php
                            // Recorre los resultados de la consulta y muestra los detalles de las órdenes en la tabla
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row['orderNumber'] . "</td>";
                                echo "<td>" . $row['productCode'] . "</td>";
                                echo "<td>" . $row['quantityOrdered'] . "</td>";
                                echo "<td>" . $row['priceEach'] . "</td>";
                                echo "<td>" . $row['orderLineNumber'] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </div>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>