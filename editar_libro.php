<?php
require_once 'conexion.php';

// Verificar que se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: inventario.php');
    exit();
}

$id = $conexion->real_escape_string($_GET['id']);
$sql = "SELECT * FROM libros WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows === 0) {
    header('Location: inventario.php');
    exit();
}

$libro = $resultado->fetch_assoc();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .book-detail-img {
            max-height: 400px;
            object-fit: contain;
        }
        .status-badge {
            font-size: 1rem;
        }
    </style>
</head>
<body class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Detalle del Libro</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <?php if (!empty($libro['foto']) && file_exists('uploads/' . $libro['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($libro['foto']) ?>" class="img-fluid book-detail-img rounded mb-3">
                    <?php else: ?>
                        <div class="bg-light p-5 rounded text-center">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No hay imagen disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
                    
                    <div class="mb-3">
                        <h5 class="d-inline">Información Básica</h5>
                        <hr class="mt-1">
                        <p><strong>ID:</strong> <?= $libro['id'] ?></p>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
                        <p><strong>Género:</strong> <?= htmlspecialchars($libro['genero']) ?></p>
                        <p><strong>Estatus:</strong> 
                            <?php 
                            $status_class = '';
                            switch($libro['estatus']) {
                                case 'Disponible': $status_class = 'bg-success'; break;
                                case 'Prestado': $status_class = 'bg-warning text-dark'; break;
                                case 'En reparación': $status_class = 'bg-danger'; break;
                                case 'Perdido': $status_class = 'bg-secondary'; break;
                            }
                            ?>
                            <span class="badge <?= $status_class ?> status-badge"><?= htmlspecialchars($libro['estatus']) ?></span>
                        </p>
                        <p><strong>Fecha de Registro:</strong> <?= date('d/m/Y H:i', strtotime($libro['fecha_registro'])) ?></p>
                    </div>
                    
                    <?php if (!empty($libro['observaciones'])): ?>
                        <div class="mb-3">
                            <h5 class="d-inline">Observaciones</h5>
                            <hr class="mt-1">
                            <p class="text-justify"><?= nl2br(htmlspecialchars($libro['observaciones'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="inventario.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al Inventario
            </a>
            <a href="editar_libro.php?id=<?= $libro['id'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Libro
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>