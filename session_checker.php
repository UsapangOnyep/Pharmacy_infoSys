<?php
// Start the session
session_start();

// Check all session variables
echo '<pre>';
print_r($_SESSION); // or use var_dump($_SESSION);
echo '</pre>';
?>
