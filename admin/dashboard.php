<?php

session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<h1>Welcome, Admin!</h1>
<a href="../logout.php">Logout</a>
