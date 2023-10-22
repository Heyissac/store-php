<?php
session_start();
require_once("dbconn.php");
global $dbconn;

$sessionUser = $_SESSION["user"];

/* ========================= Get data ========================= */
$getTables = $dbconn->prepare("SHOW TABLES FROM dbshop");
$getTables->execute();
$storeData = $getTables->fetchAll(PDO::FETCH_ASSOC);
/* ======================= Close session ====================== */
if (isset($_GET['close'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!-- ===================== HTML section ===================== -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/navbar.css">
    <!-- <link rel="stylesheet" type="text/css" href="assets/css/main_index.css"> -->
    <title>Tablas</title>
</head>

<body id="page-top">

    <!------------ Navigation bar ------------>
    <nav class="nav">
        <div class="navbar-container">
            <div class="logo">
                <a href="main_index.php">Conway</a>
            </div>
            <div class="main_list" id="mainListDiv">
                <ul>
                    <div class="dropdown">
                        <button class="dropbtn">PERFIL
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#">
                                <?php echo $sessionUser; ?>
                            </a>
                            <a href="form.php?close=1" name="close">Salir</a>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </nav>
    <!------------ Data section ------------>
    <section id="table-display">
        <div class=" body-toshow-data">
            <div class="container">
                <h1>Tablas de la base de datos</h1>
                <form method="post">
                    <label for="tables_data">Elegir tabla: </label>
                    <select name="tables_data" id="tables_data">
                        <option value="" disabled selected>Elige una opción</option>
                        <?php foreach ($storeData as $table) { ?>
                            <option value="<?php echo $table["Tables_in_dbshop"]; ?>">
                                <?php echo $table["Tables_in_dbshop"]; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="submit" name="find_data" value="Buscar">
                </form>
                <!------------ Mostrar datos ------------>
                <div id="result">
                    <?php if (isset($_POST['find_data'])) {
                        if (!empty($_POST['tables_data'])) {
                            $tableChosen = $_POST['tables_data'];

                            $tableData = $dbconn->prepare("SELECT * FROM $tableChosen");
                            $tableData->execute();
                            $showData = $tableData->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($showData)) {
                                echo '<table>';
                                echo '<tr>';
                                foreach ($showData[0] as $column => $value) {
                                    echo '<th>' . $column . '</th>';
                                }
                                echo '</tr>';
                                foreach ($showData as $row) {
                                    echo '<tr>';
                                    foreach ($row as $value) {
                                        echo '<td>' . $value . '</td>';
                                    }
                                    echo '</tr>';
                                }
                                echo '</table>';
                            }
                        }
                    } ?>
                </div>
            </div>
        </div>

        <!------- JS References ------->
        <script src="assets/js/navbar.js"></script>

    </section>
</body>

</html>