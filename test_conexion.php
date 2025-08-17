<?php
$conexion = new mysqli("localhost", "root", "", "biblioteca");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
echo "Conexión exitosa!";
$conexion->close();
?>