<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados Deshabilitados</title>
    <!-- Incluir Bootstrap para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .separacion-superior {
            margin-top: 60px; /* Ajusta el valor para controlar el espacio */
        }
    </style>
</head>
<body>
    <!-- Tabla de empleados deshabilitados -->
    <h2 class="text-center mb-4 separacion-superior">Empleados Deshabilitados</h2>
    <div class="table-responsive" style="width: 85%; margin: 0 auto;">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Número de Colaborador</th>
                    <th>Área</th>
                    <th>Placas del Vehículo</th>
                    <th>Modelo y Marca</th>
                    <th>Color del Vehículo</th>
                    <th>Código QR</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Incluir la conexión a la base de datos
                include_once '../conexion.php'; // Cambia por la ruta correcta

                // Verificar si la conexión está bien definida
                if ($conn) {
                    // Obtener empleados deshabilitados
                    $sql = "SELECT id, nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code FROM empleados_deshabilitados";
                    $stmt = $conn->query($sql);
                    $empleadosDeshabilitados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($empleadosDeshabilitados as $empleadoDeshabilitado): ?>
                        <tr>
                            <td><?php echo $empleadoDeshabilitado['id']; ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['nombre_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['numero_colaborador']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['area']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['placas_vehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['modelo_marca']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['color_vehiculo']); ?></td>
                            <td><?php echo htmlspecialchars($empleadoDeshabilitado['qr_code']); ?></td>
                            <td>
                                <a href="actualizacionesempleado.php?habilitar=<?php echo $empleadoDeshabilitado['id']; ?>" class="btn btn-success">Habilitar</a>
                            </td>
                        </tr>
                    <?php endforeach;
                } else {
                    echo "<tr><td colspan='9'>Error de conexión a la base de datos.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Contenedor para los botones -->
<div class="text-center mt-4">
    <!-- Botón para volver -->
    <button class="btn btn-secondary me-2" onclick="history.back()" aria-label="Volver a la página anterior">Volver</button>
    
    <!-- Botón para actualizar -->
    <button class="btn btn-primary" onclick="location.reload()" aria-label="Actualizar la página">Actualizar</button>
    
</div>
</body>
</html>
