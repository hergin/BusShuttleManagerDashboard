<?php
if (!isset($_SESSION['authenticated'])) {
    header('Location: index.php');
    exit;
}
?>
