<?php
// Configuración para mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión
require_once 'conexion.php';

echo "<!DOCTYPE html><html><head><title>Resultado</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='container mt-5'>";

// Verificar método de envío
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<div class='alert alert-danger'>Error: Método no permitido</div>");
}

// Procesar datos
$titulo = $conexion->real_escape_string($_POST['titulo'] ?? '');
$autor = $conexion->real_escape_string($_POST['autor'] ?? '');
$genero = $conexion->real_escape_string($_POST['genero'] ?? '');
$estatus = $conexion->real_escape_string($_POST['estatus'] ?? 'Disponible');
$observaciones = $conexion->real_escape_string($_POST['observaciones'] ?? '');

// Procesar imagen
$nombre_foto = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $directorio = 'uploads/';
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombre_foto = uniqid().'.'.$extension;
    move_uploaded_file($_FILES['foto']['tmp_name'], $directorio.$nombre_foto);
}

// Validar campos obligatorios
if (empty($titulo) || empty($autor)) {
    die("<div class='alert alert-danger'>Título y Autor son campos obligatorios</div>");
}

// Insertar en BD
$sql = "INSERT INTO libros (titulo, autor, genero, estatus, observaciones, foto) 
        VALUES ('$titulo', '$autor', '$genero', '$estatus', '$observaciones', '$nombre_foto')";

if ($conexion->query($sql)) {
    echo "<div class='alert alert-success'>Libro registrado exitosamente!</div>";
    echo "<a href='registro.html' class='btn btn-primary'>Registrar otro</a> ";
    echo "<a href='inventario.php' class='btn btn-success'>Ver inventario</a>";
} else {
    echo "<div class='alert alert-danger'>Error al registrar: ".$conexion->error."</div>";
}

$conexion->close();
echo "</body></html>";
?>