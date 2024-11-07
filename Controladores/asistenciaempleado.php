<?php
// Incluir el archivo de conexión
require '../conexion.php';

// Verificar si se ha enviado un término de búsqueda
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = htmlspecialchars($_POST['search']); // Protección contra XSS
}

// Actualizar registro de asistencia
if (isset($_POST['actualizar'])) {
    $id_ = $_POST['id'];
    $numero_colaborador_ = $_POST['numero_colaborador'];
    $fecha_entrada_ = $_POST['fecha_entrada'];
    $fecha_salida_ = $_POST['fecha_salida'];

     // Calcular la duración en horas y minutos
    $fecha_entrada_dt = new DateTime($fecha_entrada_);
    $fecha_salida_dt = new DateTime($fecha_salida_);
    $interval = $fecha_entrada_dt->diff($fecha_salida_dt);
    $duracion = $interval->format('%h horas %i minutos');

    // Actualizar datos en la base de datos
    $sql = "UPDATE asistencia_empleado SET numero_colaborador = ?, fecha_entrada = ?, fecha_salida = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$numero_colaborador_, $fecha_entrada_, $fecha_salida_, $id_])) {
        echo "<div class='alert alert-success' role='alert'>Registro de asistencia actualizado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al actualizar el registro de asistencia</div>";
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
if (!empty ($searchTerm)) {
    $sql .= " WHERE numero_colaborador LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%$searchTerm%"]);//busqueda por numero de colaborador
} else {
    $stmt = $conn->query($sql);
}
$asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
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

        <!-- Formulario de actualización -->
        <?php if (isset($_GET['id'])): 
            $id = $_GET['id'];
            $sql = "SELECT numero_colaborador, fecha_entrada, fecha_salida FROM asistencia_empleado WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="numero_colaborador" class="form-label">Número de Colaborador</label>
                <input type="text" class="form-control" id="numero_colaborador" name="numero_colaborador" value="<?php echo $asistencia['numero_colaborador']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_entrada" class="form-label">Fecha de Entrada</label>
                <input type="datetime-local" class="form-control" id="fecha_entrada" name="fecha_entrada" value="<?php echo $asistencia['fecha_entrada']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_salida" class="form-label">Fecha de Salida</label>
                <input type="datetime-local" class="form-control" id="fecha_salida" name="fecha_salida" value="<?php echo $asistencia['fecha_salida']; ?>" required>
            </div>
            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
        </form>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-2">
    <!-- Enlace a la derecha con margen superior y mejor formato -->
    <a href="../fpdf/reporteAsistenciaempleado.php" target="_blank" class="btn btn-primary d-flex align-items-center ms-3 mt-3">
        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Generar Reporte
    </a>
</div>

        <!-- Tabla de asistencia -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
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
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['numero_colaborador']; ?></td>
                    <td><?php echo $row['fecha_entrada']; ?></td>
                    <td><?php echo $row['fecha_salida']; ?></td>
                    <td><?php echo $duracion; ?></td>
                    <td>
                        <a href="asistenciaempleado.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-fill"></i> Editar
                        </a>
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
            <!-- Botón para volver -->
            <button class="btn btn-secondary me-2" onclick="history.back()">Volver</button>
            <!-- Botón para actualizar -->
            <button class="btn btn-primary" onclick="location.reload()">Actualizar</button>
        </div>

        <!-- Enlaces de Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>
</html>