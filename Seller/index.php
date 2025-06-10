<?php
$allowedPages = [
    'dashboard',
    'orders',
    'stocks',
    'employee',
    'account',
    'item-masterlist',
    'categories',
    'suppliers',
    'stocks-disposal',
    'inventory-transaction',
    'sales',
    '404'
];

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$page = basename($page);

if (!in_array($page, $allowedPages)) {
    $page = '404';
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy InfoSys - <?= ucfirst($page) ?></title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="Assets/css/shared/main.css">
    <link rel="stylesheet" href="Assets/css/shared/index.css">
    <link rel="stylesheet" href="Assets/css/shared/tables.css">
    <link rel="stylesheet" href="Assets/css/shared/modals.css">
    <link rel="stylesheet" href="Assets/css/shared/card.css">
    <link rel="stylesheet" href="Assets/css/<?= $page ?>.css">
</head>

<body>
    <div class="container">
        <?php include 'shared/header.php'; ?>
        <?php include 'shared/sidebar.php'; ?>
        <main class="main-content">
            <section class="content">
                <?php include $page . '.php'; ?>
            </section>
        </main>
    </div>

    <div id="logout-animation">
        <div class="spinner"></div>
        <p id="logout-message">Logging out...</p>
    </div>

    <!-- JS Files -->
    <script src="Assets/js/SweetAlert/sweetalert2.js"></script>
    <script src="Assets/js/main.js"></script>
    <script src="Assets/js/logout.js"></script>
    <script src="Assets/js/<?= $page ?>.js"></script>
    <!-- <script src="Assets/js/disabler-dev-tool.js"></script> -->

</body>

</html>