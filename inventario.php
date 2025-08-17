<?php
// Mostrar todos los errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';

// Configurar cabeceras HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .book-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .search-box {
            max-width: 400px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .no-image-placeholder {
            width: 60px;
            height: 80px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
    </style>
</head>
<body class="container mt-4">
    <h1 class="mb-4 text-center">Inventario de Libros</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <a href="registro.html" class="btn btn-primary mb-2">
            <i class="bi bi-plus-circle"></i> Registrar Nuevo Libro
        </a>
        
        <!-- Formulario de Búsqueda -->
        <form method="GET" class="mb-2 search-box">
            <div class="input-group">
                <input type="text" class="form-control" name="busqueda" placeholder="Buscar por título, autor o género" 
                       value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <?php if(isset($_GET['busqueda']) && !empty($_GET['busqueda'])): ?>
                    <a href="inventario.php" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php
    // Construir consulta con búsqueda
    $busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';
    
    $sql = "SELECT * FROM libros";
    
    if (!empty($busqueda)) {
        $sql .= " WHERE titulo LIKE '%$busqueda%' 
                 OR autor LIKE '%$busqueda%' 
                 OR genero LIKE '%$busqueda%'
                 OR observaciones LIKE '%$busqueda%'";
    }
    
    $sql .= " ORDER BY fecha_registro DESC";
    $resultado = $conexion->query($sql);
    
    if ($resultado->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover align-middle">';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Portada</th>';
        echo '<th>Título</th>';
        echo '<th>Autor</th>';
        echo '<th>Género</th>';
        echo '<th>Estatus</th>';
        echo '<th>Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while($libro = $resultado->fetch_assoc()) {
            // Determinar clase de estatus
            $status_class = '';
            switch($libro['estatus']) {
                case 'Disponible': $status_class = 'bg-success'; break;
                case 'Prestado': $status_class = 'bg-warning text-dark'; break;
                case 'En reparación': $status_class = 'bg-danger'; break;
                case 'Perdido': $status_class = 'bg-secondary'; break;
            }
            
            echo '<tr>';
            echo '<td>' . $libro['id'] . '</td>';
            
            // Mostrar imagen o placeholder
            if (!empty($libro['foto']) && file_exists('uploads/' . $libro['foto'])) {
                echo '<td><img src="uploads/' . htmlspecialchars($libro['foto']) . '" class="book-image img-thumbnail" 
                     onerror="this.onerror=null; this.parentNode.innerHTML=\'<div class=\"no-image-placeholder\">Error</div>\'"></td>';
            } else {
                echo '<td><div class="no-image-placeholder">';
                echo '<span>Sin imagen</span>';
                echo '</div></td>';
            }
            
            echo '<td>' . htmlspecialchars($libro['titulo']) . '</td>';
            echo '<td>' . htmlspecialchars($libro['autor']) . '</td>';
            echo '<td>' . htmlspecialchars($libro['genero']) . '</td>';
            echo '<td><span class="badge ' . $status_class . ' status-badge">' . htmlspecialchars($libro['estatus']) . '</span></td>';
            echo '<td>';
            echo '<a href="detalle_libro.php?id=' . $libro['id'] . '" class="btn btn-sm btn-info me-1" title="Ver detalle"><i class="bi bi-eye"></i></a>';
            echo '<a href="editar_libro.php?id=' . $libro['id'] . '" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        
        // Mostrar contador de resultados
        echo '<div class="mt-3 text-end text-muted">';
        echo 'Mostrando ' . $resultado->num_rows . ' libros';
        if (!empty($busqueda)) {
            echo ' para la búsqueda: "' . htmlspecialchars($busqueda) . '"';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">';
        if (!empty($busqueda)) {
            echo 'No se encontraron libros para la búsqueda: "' . htmlspecialchars($busqueda) . '"';
        } else {
            echo 'No hay libros registrados en el inventario.';
        }
        echo '</div>';
    }
    
    $conexion->close();
    ?>
    
    <!-- Iconos de Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>