<?php
require_once("dbconn.php");
global $dbconn;

session_start();
$sessionUser = $_SESSION["user"];

// Realiza una consulta para recuperar los detalles de las órdenes
$query = "SELECT orderNumber, productCode, quantityOrdered, priceEach, orderLineNumber
          FROM orderdetails";

$result = $dbconn->query($query);

$orders = $dbconn->query("SELECT customers.customerName, orders.customerNumber, COUNT(*) AS num_orders
FROM orders
LEFT JOIN customers
ON orders.customerNumber = customers.customerNumber
GROUP BY customerNumber
ORDER BY num_orders DESC
LIMIT 5;")->fetchAll(PDO::FETCH_ASSOC);

$payments = $dbconn->query("SELECT customers.customerName, payments.customerNumber, COUNT(*) AS num_payments
FROM payments
LEFT JOIN customers
ON payments.customerNumber = customers.customerNumber
GROUP BY customerNumber
ORDER BY num_payments ASC
LIMIT 5;")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/icons/order.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/order.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/header.css">
    <title>Detalles de Órdenes</title>
</head>
<header class="head">
    <div class="logo border-bottom">
        <!-- <img class="w-100" src="assets/images/geek.png" alt="" /> -->
        <a class="navbar-toggler d-block d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        </a>
    </div>
    <div id="navbarNav" class="navcol d-none d-lg-block">
        <ul>
            <li><a href="#order-data"><i class="bi fs-5 bi-folder-plus me-2"
                        style="color: #3F021F"></i>Órdenes</a>
            </li>
            <li><a href="#more-order" name="find-table"><i class="bi fs-5 bi-clipboard2-plus-fill me-2"
                        style="color: #3F021F"></i>Más órdenes</a></li>
            <li><a href="#less-order" name="detail-order"><i class="bi bi-credit-card-2-back-fill me-2"
                        style="color: #3F021F"></i>Menos pagos</a></li>
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
                    <li><a href="crud.php"><i class="bi fs-5 bi-cloud-fog-fill me-2" style="padding: 10px"></i>CRUD</a>
                    </li>
                    <li><a href="form.php"><i class="bi fs-5 bi bi-search me-2" style="padding: 10px"></i>Buscar
                            tablas</a></li>
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
    <!------------ Data section ------------>
    <div class="main-content">
        <section id="order-data" name="order-data">
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
        </section>

        <section id="more-order" name="more-order">
            <div class=" body-toshow-data">
                <div class="container">
                    <h1>Clientes con más órdenes</h1>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Número de Cliente</th>
                                    <th>Nombre del Cliente</th>
                                    <th>Cantidad de Órdenes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div>
                                    <?php
                                    foreach ($orders as $or) {
                                        echo "<tr>";
                                        echo "<td>" . $or['customerNumber'] . "</td>";
                                        echo "<td>" . $or['customerName'] . "</td>";
                                        echo "<td>" . $or['num_orders'] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </div>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section id="less-order" name="less-order">
            <div class=" body-toshow-data">
                <div class="container">
                    <h1>Clientes con menos pagos</h1>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Número de Cliente</th>
                                    <th>Nombre del Cliente</th>
                                    <th>Cantidad de pagos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div>
                                    <?php
                                    foreach ($payments as $pay) {
                                        echo "<tr>";
                                        echo "<td>" . $pay['customerNumber'] . "</td>";
                                        echo "<td>" . $pay['customerName'] . "</td>";
                                        echo "<td>" . $pay['num_payments'] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </div>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>