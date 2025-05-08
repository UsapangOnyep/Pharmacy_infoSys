<?php
$defaultPage = 'dashboard';
$page = isset($_GET['page']) ? basename($_GET['page']) : $defaultPage;

$file = __DIR__ . '/' . $page . '.php';

if (file_exists($file)) {
    include $file;
} else {
    echo '<p>Page not found.</p>';
}
?>