<?php
require_once(dirname(__FILE__) . '/config.php');

$username = $_POST['username'];
$password = $_POST['password'];

if($username=="admin" && $password==ADMIN_PASSWORD) {
	session_start();
        $_SESSION['authenticated'] = true;
        header("Location: ./Pages/Users.php");
        exit;
} else {
	header("Location: index.php");
}
?>
