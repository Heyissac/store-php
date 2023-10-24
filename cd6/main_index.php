<?php
session_start();
$sessionUser = $_SESSION['user'];

include_once("dbconn.php");
global $dbconn;

/* ========================= Get data ======================== */
$fetchData = $dbconn->prepare("SELECT * FROM user WHERE user = :user");
$fetchData->execute(['user' => $sessionUser]);
$storeData = $fetchData->fetchAll();

if ($storeData != '') {
    foreach ($storeData as $row) {
        $userName = $row['name'];
        $userLastname = $row['lastname'];
        $databaseUser = $row['user'];
    }
}
/* ======================== DB commands ======================= */
$databaseName = $dbconn->prepare("SELECT DATABASE()");
$databaseName->execute();
/* ======================== Show Tables ======================= */
if (isset($_POST['show-tables']) || isset($_GET['find-table'])) {
    header('Location: form.php');
}
/* ======================= Details Tables ===================== */
if (isset($_GET['detail-order'])) {
    header('Location: order_details.php');
}

if (isset($_GET['detail-product'])) {
    header('Location: product_details.php');
}

if (isset($_GET['crud-button'])) {
    header('Location: crud.php');
}
/* ======================= Close session ====================== */
if (isset($_GET['close'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Geekopolys</title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/css/profile.css" />
</head>
<header class="head">
    <div class="logo border-bottom">
        <img class="w-100" src="assets/images/geek.png" alt="" />
        <a class="navbar-toggler d-block d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </a>
    </div>
    <div id="navbarNav" class="navcol d-none d-lg-block">
        <ul>
            <li><a href="#"><i class="bi bi-house-fill fs-5 me-2" style="color: #3F021F"></i>Inicio</a></li>
            <li><a href="#about"><i class="bi fs-5 bi-info-circle-fill me-2" style="color: #3F021F"></i>Acerca</a></li>
            <li><a href="form.php?find-table=1" name="find-table"><i class="bi fs-5 bi-newspaper me-2"
                        style="color: #3F021F"></i>Buscar tablas</a></li>
            <li><a href="order_details.php?detail-oder=1" name="detail-order"><i class="bi fs-5 bi-list-ul me-2"
                        style="color: #3F021F"></i>Ordenes</a></li>
            <li><a href="product_details.php?detail-product=1" name="detail-product"><i
                        class="bi fs-5 bi-basket-fill me-2" style="color: #3F021F"></i>Productos</a></li>
            <li><a href="crud.php?crud-button=1" name="crud-button"><i class="bi fs-5 bi-cloud-fog-fill me-2"
                        style="color: #3F021F"></i>CRUD</a></li>
            <li><a href="login.php?close=1" name="close"><i class="bifs-5  bi-box-arrow-left me-2"
                        style="color: #3F021F"></i>Salir</a></li>
        </ul>
    </div>
</header>
<div class="main-content">
    <!----------------------- Personal info ----------------------->
    <form method="POST">
        <div class="profile-head">
            <div class="row vh-100">
                <div class="col-xl-6 text-center mx-auto align-self-center ">
                    <div class="imgcover mb-4">
                        <img src="assets/images/profile.png" class="rounded-pill bg-white p-2 shadow" alt="">
                    </div>
                    <?php foreach ($storeData as $row) { ?>
                    <b class="fs-6">
                        <?php echo $row['name'] . " " . $row['lastname']; ?>
                    </b>
                    <h1 class="fw-bold mb-4 fs-1">
                        <?php echo "@" . $row['user'] ?>
                    </h1>
                    <?php } ?>
                    <p>De vez en cuando, una nueva tecnología, un antiguo problema y una gran idea se convierten en una
                        innovación.</p>
                    <ul>
                        <li></li>
                    </ul>
                    <button type="submit" class="btn btn-outline-primary fw-bolder fs-7 px-4 py-2 mt-3 rounded-pill"
                        name="show-tables">Ver tablas</button>
                </div>
            </div>
        </div>
    </form>
    <!----------------------- About info ----------------------->
    <div id="about" class="about px-4 bg-white py-5">
        <div class="titie-row row mb-3">
            <h2 class="fw-bolder">
                <?php if ($row = $databaseName->fetch()) {
                    echo "Base de datos: " . $row[0];
                } ?>
            </h2>
        </div>
        <div class="row">
            <div class="col-md-7">
                <p><b>
                        <?php echo "Gestor: " . $userName . " " . $userLastname; ?>
                    </b></p>
                <p class="pt-2 fs-6 text-justify">La base de datos de Geekopolis Hub, la tienda en línea líder en su
                    sector, se erige como una fuente invaluable de información para nuestros gestores y analistas. En
                    ella, encontrarán los datos cruciales necesarios para trazar el rumbo de la empresa y extraer
                    conclusiones fundamentadas en estadísticas sólidas. Este recurso se convierte en la clave para
                    mejorar nuestra ventaja competitiva, permitiéndonos tomar decisiones estratégicas informadas y
                    mantenernos a la vanguardia de la industria. En Geekopolis Hub, estamos comprometidos a emplear los
                    datos de manera efectiva en la búsqueda constante de la excelencia y el éxito empresarial."</p>

                <h4 class=" fs-5 my-3 mt-4 fw-bolder text-justify">Lenguajes en los que se cimenta la base de datos:
                </h4>
                <p>La plataforma que impulsa nuestra página web se sustenta en una armoniosa sinfonía de lenguajes y
                    tecnologías, trabajando en conjunto para brindar una experiencia funcional excepcional.
                </p>

                <div class="row skill-set">
                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">PHP</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 46.7%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">46.7%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">CSS</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 48%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">48%</div>
                        </div>
                    </div>
                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">JavaScript</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 5.3%;" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">5.3%</div>
                        </div>
                    </div>

                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">HTML</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">85%</div>
                        </div>
                    </div>

                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">MySQL</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 55%;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">55%</div>
                        </div>
                    </div>

                    <div class="col-md-6 py-3">
                        <h6 class="fw-bold">SCSS</h6>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-label="Example with label"
                                style="width: 66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">66%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <img src="assets/images/profile.png" alt="">
            </div>
        </div>
    </div>
</div>
<!----------------- JS imports ----------------->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<body>
</body>

</html>