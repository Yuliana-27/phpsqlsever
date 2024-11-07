<?php
// Incluye la conexión
require '../conexion.php';

try {
    // Consulta para obtener los datos de la empresa
    $stmt = $conn->prepare("SELECT id_empresa, nombre, telefono, ubicacion, ruc FROM empresa WHERE id_empresa = :id_empresa");
    $stmt->execute([':id_empresa' => 1]); // Ajusta el ID según corresponda
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Información de la Empresa</title>
    <style>
        /* Estilos generales del contenedor */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 400px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .form-container h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #666;
        }
        .form-group input {
            width: 90%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-submit, .btn-secondary {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-submit {
            background-color: #007bff;
            margin-bottom: 10px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .alert-success {
            margin-top: 15px;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            display: none; /* Ocultar inicialmente */
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2> Información de la Empresa</h2>
    <form id="formEmpresa">
        <input type="hidden" name="id_empresa" value="<?php echo $empresa['id_empresa']; ?>">

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empresa['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empresa['telefono']); ?>" required>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($empresa['ubicacion']); ?>" required>
        </div>

        <div class="form-group">
            <label for="ruc">RUC:</label>
            <input type="text" id="ruc" name="ruc" value="<?php echo htmlspecialchars($empresa['ruc']); ?>" required>
        </div>

        <!-- Botón de Guardar Cambios -->
        <button type="button" class="btn-submit" onclick="actualizarEmpresa()">Guardar Cambios</button>
    </form>

    <!-- Mensaje de éxito -->
    <div class="alert-success" id="successMessage">¡Cambios guardados exitosamente!</div>

    <!-- Botón Volver -->
    <button class="btn-secondary" onclick="history.back()" aria-label="Volver a la página anterior">Volver</button>
</div>

<script>
function actualizarEmpresa() {
    const form = document.getElementById('formEmpresa');
    const formData = new FormData(form);

    fetch('procesar_editar_empresa.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // Verifica la respuesta en la consola

        if (data.includes("Información actualizada exitosamente")) {
            document.getElementById('successMessage').style.display = 'block';

            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
        } else {
            alert(data); // Mostrar cualquier mensaje de error
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>