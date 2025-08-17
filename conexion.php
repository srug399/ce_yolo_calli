<?php
$servidor = "localhost";
$usuario = "root";
$password = ""; // Por defecto en XAMPP no hay contraseña
$base_datos = "biblioteca";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres
$conexion->set_charset("utf8");
?>