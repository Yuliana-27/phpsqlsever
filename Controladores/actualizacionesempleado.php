<?php
// Incluir el archivo de conexión
require '../conexion.php';
// Incluir la librería PHP QR Code
include('../phpqrcode/qrlib.php');
session_start();
$rol = $_SESSION['rol']; // Obtener el rol del usuario


// Inicializar variable para la búsqueda
$busqueda = '';

// Verificar si se ha enviado un término de búsqueda
if (isset($_POST['buscar'])) {
    $busqueda = $_POST['numero_colaborador'];
}

// Actualizar empleado
if ($rol === 'admin' && isset($_POST['actualizar'])) {
    $id_ = $_POST['id'];
    $nombre_apellido_ = $_POST['nombre_apellido'];
    $numero_colaborador_ = $_POST['numero_colaborador'];
    $area_ = $_POST['area'];
    $placas_vehiculo_ = $_POST['placas_vehiculo'];
    $modelo_marca_ = $_POST['modelo_marca'];
    $color_vehiculo_ = $_POST['color_vehiculo'];

    // Verificar si el número de colaborador ya está registrado en otro empleado
    $sql = "SELECT COUNT(*) FROM empleados WHERE numero_colaborador = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$numero_colaborador_, $id_]);
    $colaboradorExistente = $stmt->fetchColumn();

    if ($colaboradorExistente > 0) {
        // Si el número de colaborador ya está registrado, mostrar un error
        echo "<div class='alert alert-danger' role='alert'>Error: El número de colaborador ya está registrado para otro empleado</div>";
    } else {
        // Verificar si las placas ya están registradas en otro empleado
        $sql = "SELECT COUNT(*) FROM empleados WHERE placas_vehiculo = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$placas_vehiculo_, $id_]);
        $placasExistentes = $stmt->fetchColumn();

        if ($placasExistentes > 0) {
            // Si las placas ya están registradas, mostrar un error
            echo "<div class='alert alert-danger' role='alert'>Error: Las placas del vehículo ya están registradas para otro empleado</div>";
        } else {
            // Verificar el QR y el número de colaborador actual
            $sql = "SELECT qr_code, numero_colaborador FROM empleados WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Ruta del archivo QR existente
            $oldQrFilePath = $empleado['qr_code'];

            // Si el número de colaborador ha cambiado, renombrar el archivo QR
            if ($empleado['numero_colaborador'] != $numero_colaborador_) {
                $newQrFilePath = "../img_qr/qr_" . $numero_colaborador_ . ".png";

                // Verificar si el nuevo archivo ya existe, en cuyo caso lo eliminamos
                if (file_exists($newQrFilePath)) {
                    unlink($newQrFilePath);
                }

                // Renombrar el archivo QR existente
                if (file_exists($oldQrFilePath)) {
                    rename($oldQrFilePath, $newQrFilePath);
                }
            } else {
                // Si no ha cambiado, usar el archivo QR existente
                $newQrFilePath = $oldQrFilePath;
            }

            // Generar el contenido del QR usando los datos actualizados
            $qrContent = "empleado|$numero_colaborador_|\nNombre:$nombre_apellido_|\nÁrea:$area_|\nPlacas:$placas_vehiculo_|\nVehículo:$modelo_marca_|\n$color_vehiculo_";

            // Actualizar el contenido del QR (sobrescribir el archivo QR existente)
            QRcode::png($qrContent, $newQrFilePath);

            // Actualizar los datos en la base de datos
            $sql = "UPDATE empleados SET nombre_apellido = ?, numero_colaborador = ?, area = ?, placas_vehiculo = ?, modelo_marca = ?, color_vehiculo = ?, qr_code = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute([$nombre_apellido_, $numero_colaborador_, $area_, $placas_vehiculo_, $modelo_marca_, $color_vehiculo_, $newQrFilePath, $id_])) {
                echo "<div class='alert alert-success' role='alert'>Empleado y código QR actualizados correctamente</div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error al actualizar el empleado</div>";
            }
        }
    }
}

// Eliminar empleado
if (isset($_GET['eliminar']) && $rol === 'admin') {
    $id = $_GET['eliminar'];

    $sql = "DELETE FROM empleados WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        echo "<div class='alert alert-success' role='alert'>Empleado eliminado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al eliminar el empleado</div>";
    }
}

// Habilitar/deshabilitar empleado
if (($rol === 'admin') && (isset($_GET['habilitar']) || isset($_GET['deshabilitar']))) {
    $id = isset($_GET['habilitar']) ? $_GET['habilitar'] : $_GET['deshabilitar'];

    if (isset($_GET['deshabilitar'])) {
        // Mover el empleado a la tabla empleados_deshabilitados
        $sql = "INSERT INTO empleados_deshabilitados (nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code)
                SELECT nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code
                FROM empleados WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // Eliminar el empleado de la tabla empleados
        $sql = "DELETE FROM empleados WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        echo "<div class='alert alert-success' role='alert'>Empleado deshabilitado correctamente</div>";
    } else {
        // Mover el empleado de la tabla empleados_deshabilitados a empleados
        $sql = "INSERT INTO empleados (nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code)
                SELECT nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code
                FROM empleados_deshabilitados WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // Eliminar el empleado de la tabla empleados_deshabilitados
        $sql = "DELETE FROM empleados_deshabilitados WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        echo "<div class='alert alert-success' role='alert'>Empleado habilitado correctamente</div>";
    }
} else {
    // Si no es admin, mostrar un mensaje cuando intente realizar una acción
    if (isset($_POST['actualizar']) || isset($_GET['eliminar']) || isset($_GET['habilitar']) || isset($_GET['deshabilitar'])) {
        
    }
}


// Obtener empleados (con búsqueda)
$sql = "SELECT id, nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code, estado FROM empleados";
if ($busqueda) {
    $sql .= " WHERE numero_colaborador LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%$busqueda%"]);
} else {
    $stmt = $conn->query($sql);
}
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="text-center mb-4">Gestión de Colaboradores</h1>

        <!-- Formulario de búsqueda -->
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar por número de colaborador" name="numero_colaborador" value="<?php echo htmlspecialchars($busqueda); ?>" required>
                <button class="btn btn-outline-secondary" type="submit" name="buscar">Buscar</button>
            </div>
        </form>

        <!-- Formulario de actualización -->
        <?php if (isset($_GET['id'])): 
            $id = $_GET['id'];
            $sql = "SELECT nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code FROM empleados WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="nombre_apellido" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre_apellido" name="nombre_apellido" value="<?php echo $empleado['nombre_apellido']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="numero_colaborador" class="form-label">Numero de Colaborador</label>
                <input type="text" class="form-control" id="numero_colaborador" name="numero_colaborador" value="<?php echo $empleado['numero_colaborador']; ?>" required>
            </div>
            <div class="mb-3">
    <label for="area" class="form-label">Área</label>
    <select class="form-select" id="area" name="area" required>
        <option value="" disabled selected>Selecciona un Área</option>
        <option value="RRHH" <?php if($empleado['area'] == 'RRHH') echo 'selected'; ?>>RRHH</option>
        <option value="Servicios Técnicos" <?php if($empleado['area'] == 'Servicios Técnicos') echo 'selected'; ?>>Servicios Técnicos</option>
        <option value="Lavanderia" <?php if($empleado['area'] == 'Lavanderia') echo 'selected'; ?>>Lavanderia</option>
        <option value="Roperia" <?php if($empleado['area'] == 'Roperia') echo 'selected'; ?>>Roperia</option>
        <option value="Bodas" <?php if($empleado['area'] == 'Bodas') echo 'selected'; ?>>Bodas</option>
        <option value="Entretenimiento" <?php if($empleado['area'] == 'Entretenimiento') echo 'selected'; ?>>Entretenimiento</option>
        <option value="Servibar" <?php if($empleado['area'] == 'Servibar') echo 'selected'; ?>>Servibar</option>
        <option value="Travel Club" <?php if($empleado['area'] == 'Travel Club') echo 'selected'; ?>>Travel Club</option>
        <option value="Spa" <?php if($empleado['area'] == 'Spa') echo 'selected'; ?>>Spa</option>
        <option value="It" <?php if($empleado['area'] == 'It') echo 'selected'; ?>>It</option>
        <option value="Recepción" <?php if($empleado['area'] == 'Recepción') echo 'selected'; ?>>Recepción</option>
        <option value="Redes Sociales" <?php if($empleado['area'] == 'Redes Sociales') echo 'selected'; ?>>Redes Sociales</option>
        <option value="Butler" <?php if($empleado['area'] == 'Butler') echo 'selected'; ?>>Butler</option>
        <option value="Seguridad" <?php if($empleado['area'] == 'Seguridad') echo 'selected'; ?>>Seguridad</option>
        <option value="Room Service" <?php if($empleado['area'] == 'Room Service') echo 'selected'; ?>>Room Service</option>
        <option value="Palladium Rewards" <?php if($empleado['area'] == 'Palladium Rewards') echo 'selected'; ?>>Palladium Rewards</option>
    </select>
</div>

            <div class="mb-3">
                <label for="placas_vehiculo" class="form-label">Placas del Vehículo</label>
                <input type="text" class="form-control" id="placas_vehiculo" name="placas_vehiculo" value="<?php echo $empleado['placas_vehiculo']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="modelo_marca" class="form-label">Modelo y Marca del Vehículo</label>
                <input type="text" class="form-control" id="modelo_marca" name="modelo_marca" value="<?php echo $empleado['modelo_marca']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="color_vehiculo" class="form-label">Color del Vehículo</label>
                <input type="text" class="form-control" id="color_vehiculo" name="color_vehiculo" value="<?php echo $empleado['color_vehiculo']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="qr_code" class="form-label">Codigo QR</label>
                <input type="text" class="form-control" id="qr_code" name="qr_code" value="<?php echo $empleado['qr_code']; ?>" required>
            </div>
            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
        </form>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-2">
    <!-- Enlace a la derecha con margen superior y mejor formato -->
    <a href="empleados_deshabilitados.php" class="btn btn-danger btn-lg mt-3">
        <i class="bi bi-x-circle-fill"></i> Empleados Deshabilitados
    </a>

    <a href="../fpdf/reporteEmpleado.php" target="_blank" class="btn btn-primary d-flex align-items-center ms-3 mt-3">
        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Generar Reporte
    </a>
</div>

<!-- Modal para mostrar el QR a gran tamaño -->
<div class="modal fade" id="modalQr" tabindex="-1" aria-labelledby="modalQrLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQrLabel">Código QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qrContainer" class="d-flex align-items-center">
                    <!-- Imagen del QR a la izquierda -->
                    <img src="" id="qrImage" alt="QR Code" class="img-fluid me-3" style="width: 150px; height: auto;">
                    
                    <!-- Información del empleado a la derecha -->
                    <div id="qrInfo" style="text-align: left;">
                        <!-- Los detalles se llenarán con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Tabla de empleados -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Numero de Colaborador</th>
                    <th>Área</th>
                    <th>Placas del Vehículo</th>
                    <th>Modelo y Marca</th>
                    <th>Color del Vehículo</th>
                    <th>Código QR</th>
                    <th>Estado</th> <!-- Nueva columna para el estado -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre_apellido']; ?></td>
                    <td><?php echo $row['numero_colaborador']; ?></td>
                    <td><?php echo $row['area']; ?></td>
                    <td><?php echo $row['placas_vehiculo']; ?></td>
                    <td><?php echo $row['modelo_marca']; ?></td>
                    <td><?php echo $row['color_vehiculo']; ?></td>
                    <td>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalQr" onclick="showQr('<?php echo $row['qr_code']; ?>', '<?php echo $row['nombre_apellido']; ?>', '<?php echo $row['numero_colaborador']; ?>')">
                    <img src="<?php echo $row['qr_code']; ?>" alt="QR Code" width="50">
                </a>
            </td>
            <?php if ($rol === 'admin'): ?>
                    <td><?php echo $row['estado'] ? 'Habilitado' : 'Deshabilitado'; ?></td> <!-- Mostrar estado -->
                    <td>
                        <a href="actualizacionesempleado.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="actualizacionesempleado.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este empleado?');">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                        <?php if ($row['estado']): ?>
                        <a href="actualizacionesempleado.php?deshabilitar=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm" onclick="return confirm('¿Estás seguro de deshabilitar este empleado?');">
                        <i class="bi bi-toggle-off"></i>
                        </a>
                        <?php else: ?>
                        <a href="actualizacionesempleado.php?habilitar=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Estás seguro de habilitar este empleado?');">
                        <i class="bi bi-toggle-on"></i>
                        </a>
                        <?php endif; ?>
                        <?php else: ?>
                
            <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<!-- Contenedor para los botones -->
<div class="text-center mt-4">
    <!-- Botón para volver -->
    <button class="btn btn-secondary me-2" onclick="history.back()" aria-label="Volver a la página anterior">Volver</button>
    
    <!-- Botón para actualizar -->
    <button class="btn btn-primary" onclick="location.reload()" aria-label="Actualizar la página">Actualizar</button>
    
</div>

<script>
    function showQr(qrImage, nombre, numeroColaborador) {
        // Establecer el QR de gran tamaño en el modal
        document.getElementById("qrImage").src = qrImage;

        // Mostrar la información correspondiente
        document.getElementById("qrInfo").innerHTML = `
            <strong>Nombre:</strong> ${nombre}<br>
            <strong>Número de Colaborador:</strong> ${numeroColaborador}
        `;
    }
</script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
