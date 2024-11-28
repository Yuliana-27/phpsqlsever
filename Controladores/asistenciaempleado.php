<?php
// Incluir el archivo de conexión
require '../conexion.php';

// Verificar si se ha enviado un término de búsqueda
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = htmlspecialchars($_POST['search']); // Protección contra XSS
}

// Eliminar múltiples registros de asistencia
if (isset($_POST['eliminar_seleccionados']) && !empty($_POST['seleccionados'])) {
    $ids = $_POST['seleccionados'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM asistencia_empleado WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute($ids)) {
        echo "<div class='alert alert-success' role='alert'>Registros de asistencia eliminados correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al eliminar los registros de asistencia</div>";
    }
}

// Eliminar registro de asistencia
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $sql = "DELETE FROM asistencia_empleado WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        echo "<div class='alert alert-success' role='alert'>Registro de asistencia eliminado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al eliminar el registro de asistencia</div>";
    }
}

// Obtener registros de asistencia (con búsqueda)
$sql = "SELECT id, numero_colaborador, fecha_entrada, fecha_salida FROM asistencia_empleado";
if (!empty($searchTerm)) {
    $sql .= " WHERE numero_colaborador LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%$searchTerm%"]); // búsqueda por número de colaborador
} else {
    $stmt = $conn->query($sql);
}
$asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium Hotel Group</title>
    <link rel="icon" href="../img/vista.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Gestión de Asistencia de Colaboradores</h1>

        <!-- Formulario de búsqueda -->
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por número de colaborador" value="<?php echo $searchTerm; ?>">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </div>
        </form>

        <!-- Botones para generar reportes -->
        <div class="d-flex justify-content-between mb-3">
            <a href="../fpdf/reporteAsistenciaempleado.php" target="_blank" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Generar Reporte
            </a>
            <form method="POST" action="../fpdf/reporteAsistenciaFecha.php" target="_blank" class="d-flex align-items-center">
                <input type="date" name="fecha_inicio" class="form-control me-2" required>
                <input type="date" name="fecha_fin" class="form-control me-2" required>
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-calendar2-range-fill me-2"></i> R.Fecha
                </button>
            </form>
        </div>

        <!-- Tabla de asistencia -->
        <form method="POST">
            <table class="table table-striped table-hover table-responsive-sm">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>ID</th>
                        <th>Número de Colaborador</th>
                        <th>Fecha de Entrada</th>
                        <th>Fecha de Salida</th>
                        <th>Duración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistencias as $row): 
                        $duracion = '';
                        if ($row['fecha_entrada'] && $row['fecha_salida']) {
                            $entrada = new DateTime($row['fecha_entrada']);
                            $salida = new DateTime($row['fecha_salida']);
                            $interval = $entrada->diff($salida);
                            $duracion = $interval->format('%h horas %i minutos');
                        }
                    ?>
                    <tr>
                        <td><input type="checkbox" name="seleccionados[]" value="<?php echo $row['id']; ?>"></td>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['numero_colaborador']; ?></td>
                        <td><?php echo $row['fecha_entrada']; ?></td>
                        <td><?php echo $row['fecha_salida']; ?></td>
                        <td><?php echo $duracion; ?></td>
                        <td>
                            <a href="asistenciaempleado.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este registro de asistencia?');">
                                <i class="bi bi-trash-fill"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Contenedor para los botones -->
            <div class="text-center mt-4">
                <button class="btn btn-secondary me-2" onclick="history.back()">Volver</button>
                <button class="btn btn-primary" onclick="location.reload()">Actualizar</button>
                <button type="submit" name="eliminar_seleccionados" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar los registros seleccionados?');">Eliminar Seleccionados</button>
            </div>
        </form>
    </div>

    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('input[name="seleccionados[]"]');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
