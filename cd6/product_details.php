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

//Leer product line
$products = $dbconn->query("SELECT productLine,
productCode,
productName,
productScale,
productVendor,
productDescription,
quantityInStock,
buyPrice,
MSRP,
COUNT(*) AS número_de_productos
FROM products
GROUP BY productLine, productCode;")->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="assets/images/icons/products.png" type="image/x-icon">
    <title>Productos con Línea de Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/product.css">
    <link rel="stylesheet" type="text/css" href="assets/css/header.css">
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
            <li><a href="#order-form"><i class="bi bi-card-text fs-5 me-2" style="color: #3F021F"></i>Orden</a>
            </li>
            <li><a href="#grouped-form"><i class="bi fs-5 bi-collection-fill me-2"
                        style="color: #3F021F"></i>Agrupación</a>
            </li>
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
    <div class="main-content">
        <section id="order-form" name="order-form">
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
        </section>

        <section id="grouped-form" name="group-form">
            <div class="body-toshow-data">
                <div class="container">
                    <div class="table-container">
                        <h1 class="table-header">Agrupación de Productos por ProductLine</h1>
                        <table class="data-table">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Linea de Producto</th>
                                <th>Escala</th>
                                <th>Vendedor</th>
                                <th>Descripción</th>
                                <th>Cantidad Stock</th>
                                <th>Precio</th>
                                <th>MSRP</th>
                            </tr>
                            <?php foreach ($products as $pr) { ?>
                            <tr>
                                <td>
                                    <?php echo $pr['productCode']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['productName']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['productLine']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['productScale']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['productVendor']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['productDescription']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['quantityInStock']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['buyPrice']; ?>
                                </td>
                                <td>
                                    <?php echo $pr['MSRP']; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>