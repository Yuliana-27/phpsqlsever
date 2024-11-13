<?php
// Incluir el archivo de conexión
require '../conexion.php';
// Incluir la librería PHP QR Code
include('../phpqrcode/qrlib.php');

// Búsqueda de proveedor
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = htmlspecialchars($_POST['search']); // Protección contra XSS
}

// Actualizar proveedor
if (isset($_POST['actualizar'])) {
    $id_ = $_POST['id'];
    $nombre_apellido_ = $_POST['nombre_apellido'];
    $proveedor_ = $_POST['proveedor'];
    $placas_vehiculos_ = $_POST['placas_vehiculos'];
    $modelo_marca_ = $_POST['modelo_marca'];
    $color_vehiculo_ = $_POST['color_vehiculo'];

    // Verificar si la placa ya existe para otro proveedor
    $sql = "SELECT id FROM proveedores WHERE placas_vehiculos = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$placas_vehiculos_, $id_]);
    $existingPlaca = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPlaca) {
        echo "<div class='alert alert-danger' role='alert'>Error: Las placas del vehículo ya están registradas para otro proveedor.</div>";
    } else {
        // Verificar el QR y el proveedor actual
        $sql = "SELECT qr_code, proveedor FROM proveedores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ruta del archivo QR existente
        $oldQrFilePath = $proveedor['qr_code'];

        // Si el proveedor ha cambiado, renombrar el archivo QR
        if ($proveedor['proveedor'] != $proveedor_) {
            $newQrFilePath = "../img_qr/qr_" . $proveedor_ . ".png";
            
            // Renombrar el archivo QR existente
            if (file_exists($oldQrFilePath)) {
                rename($oldQrFilePath, $newQrFilePath);
            }
        } else {
            // Si no ha cambiado, usar el archivo QR existente
            $newQrFilePath = $oldQrFilePath;
        }

        // Generar el contenido del QR usando los datos actualizados
        $qrContent = "proveedor $proveedor_ \nNombre: $nombre_apellido_ \nPlacas: $placas_vehiculos_ \nVehículo: $modelo_marca_ ($color_vehiculo_)";

        // Actualizar el contenido del QR (sobrescribir el archivo QR existente)
        QRcode::png($qrContent, $newQrFilePath);

        // Guardar la ruta o el nombre del archivo QR en la base de datos
        $qr_code_ = $newQrFilePath;

        $sql = "UPDATE proveedores SET nombre_apellido = ?, proveedor = ?, placas_vehiculos = ?, modelo_marca = ?, color_vehiculo = ?, qr_code = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$nombre_apellido_, $proveedor_, $placas_vehiculos_, $modelo_marca_, $color_vehiculo_, $qr_code_, $id_])) {
            echo "<div class='alert alert-success' role='alert'>Proveedor y código QR actualizados correctamente</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error al actualizar el proveedor</div>";
        }
    }
}


// Eliminar proveedor
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $sql = "DELETE FROM proveedores WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        echo "<div class='alert alert-success' role='alert'>Proveedor eliminado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al eliminar el proveedor</div>";
    }
}

// Obtener proveedores
$sql = "SELECT id, nombre_apellido, proveedor, placas_vehiculos, modelo_marca, color_vehiculo, qr_code, estado FROM proveedores";
if (!empty($searchTerm)) {
    $sql .= " WHERE proveedor LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%$searchTerm%"]); // Búsqueda por nombre de proveedor
} else {
    $stmt = $conn->query($sql);
}
$proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Habilitar/Deshabilitar proveedor
if (isset($_GET['habilitar']) || isset($_GET['deshabilitar'])) {
    $id = isset($_GET['habilitar']) ? $_GET['habilitar'] : $_GET['deshabilitar'];
    $estado = isset($_GET['habilitar']) ? 1 : 0; // 1 para habilitar, 0 para deshabilitar

    $sql = "UPDATE proveedores SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$estado, $id])) {
        echo "<div class='alert alert-success' role='alert'>Proveedor " . (isset($_GET['habilitar']) ? "habilitado" : "deshabilitado") . " correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al " . (isset($_GET['habilitar']) ? "habilitar" : "deshabilitar") . " el proveedor</div>";
    }
}
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
        <h1 class="text-center mb-4">Gestión de Proveedores</h1>

        <!-- Formulario de búsqueda -->
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por proveedor" value="<?php echo $searchTerm; ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <!-- Formulario de actualización -->
        <?php if (isset($_GET['id'])): 
            $id = $_GET['id'];
            $sql = "SELECT nombre_apellido, proveedor, placas_vehiculos, modelo_marca, color_vehiculo, qr_code FROM proveedores WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="nombre_apellido" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre_apellido" name="nombre_apellido" value="<?php echo $proveedor['nombre_apellido']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="proveedor" class="form-label">Proveedor</label>
                <input type="text" class="form-control" id="proveedor" name="proveedor" value="<?php echo $proveedor['proveedor']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="placas_vehiculos" class="form-label">Placas del Vehículo</label>
                <input type="text" class="form-control" id="placas_vehiculos" name="placas_vehiculos" value="<?php echo $proveedor['placas_vehiculos']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="modelo_marca" class="form-label">Modelo y Marca del Vehículo</label>
                <input type="text" class="form-control" id="modelo_marca" name="modelo_marca" value="<?php echo $proveedor['modelo_marca']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="color_vehiculo" class="form-label">Color del Vehículo</label>
                <input type="text" class="form-control" id="color_vehiculo" name="color_vehiculo" value="<?php echo $proveedor['color_vehiculo']; ?>" required>
            </div>
            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
        </form>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-2">
        <!-- Enlace a la derecha con margen superior y mejor formato -->

    <a href="../fpdf/reporteProveedor.php" target="_blank" class="btn btn-primary d-flex align-items-center ms-3 mt-3">
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

        <!-- Tabla de proveedores -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Proveedor</th>
                    <th>Placas del Vehículo</th>
                    <th>Modelo y Marca</th>
                    <th>Color del Vehículo</th>
                    <th>QR</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proveedores as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre_apellido']; ?></td>
                    <td><?php echo $row['proveedor']; ?></td>
                    <td><?php echo $row['placas_vehiculos']; ?></td>
                    <td><?php echo $row['modelo_marca']; ?></td>
                    <td><?php echo $row['color_vehiculo']; ?></td>
                    <td>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalQr" onclick="showQr('<?php echo $row['qr_code']; ?>', '<?php echo $row['nombre_apellido']; ?>', '<?php echo $row['proveedor']; ?>')">
                    <img src="<?php echo $row['qr_code']; ?>" alt="QR Code" width="50">
                </a>
            </td>
                    <td><?php echo $row['estado'] ? 'Habilitado' : 'Deshabilitado'; ?></td>
                    <td>
                        <a href="actualizacionesproveedor.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="actualizacionesproveedor.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este proveedor?');">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                        <?php if ($row['estado']): ?>
                            <a href="actualizacionesproveedor.php?deshabilitar=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm" onclick="return confirm('¿Estás seguro de deshabilitar este proveedor?');">
                                <i class="bi bi-toggle-off"></i>
                            </a>
                        <?php else: ?>
                            <a href="actualizacionesproveedor.php?habilitar=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Estás seguro de habilitar este proveedor?');">
                                <i class="bi bi-toggle-on"></i>
                            </a>
                        <?php endif; ?>
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

        <script>
    function showQr(qrImage, nombre, proveedor) {
        // Establecer el QR de gran tamaño en el modal
        document.getElementById("qrImage").src = qrImage;

        // Mostrar la información correspondiente
        document.getElementById("qrInfo").innerHTML = `
            <strong>Nombre:</strong> ${nombre}<br>
            <strong>Proveedor:</strong> ${proveedor}
        `;
    }
</script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>
</html>
