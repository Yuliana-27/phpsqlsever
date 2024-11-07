<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium Hotel Group</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery para AJAX -->
    <link rel="icon" href="img/vista.png" type="image/png">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Cambiar Contraseña</h2>

        <div id="message" class="alert d-none"></div> <!-- Aquí se mostrarán los mensajes de éxito o error -->

        <?php
        // Incluir la conexión a la base de datos
        require 'conexion.php';

        // Activar el modo de error de PDO para mostrar errores
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_GET['token'])) {
            $token = $_GET['token'];

            try {
                // Verificar si el token es válido
                $query = $conn->prepare("SELECT id FROM usuario WHERE TRIM(token) = TRIM(:token)");
                $query->bindParam(":token", $token);

                // Ejecutar la consulta
                if ($query->execute()) {
                    $result = $query->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                        // Si se encuentra el token, mostrar el formulario de cambio de contraseña
                        ?>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form id="passwordForm">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña:</label>
                                        <input type="password" name="password" class="form-control" id="password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    } else {
                        // Si no se encuentra el token, mostrar un mensaje de error
                        echo '<div class="alert alert-danger mt-3">Token inválido.</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning mt-3">Error al ejecutar la consulta SQL.</div>';
                }
            } catch (PDOException $e) {
                // Capturar errores de la base de datos y mostrarlos
                echo '<div class="alert alert-danger mt-3">Error en la consulta SQL: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-warning mt-3">No se ha proporcionado un token válido.</div>';
        }
        ?>

    </div>

    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para manejar el envío del formulario con AJAX -->
    <script>
        $(document).ready(function() {
            // Manejar el envío del formulario
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault(); // Evitar el comportamiento predeterminado del formulario

                // Enviar los datos del formulario usando AJAX
                $.ajax({
                    type: 'POST',
                    url: 'cambiar_contrasena.php', // Archivo PHP que maneja el cambio de contraseña
                    data: $(this).serialize(), // Serializar los datos del formulario
                    success: function(response) {
                        // Mostrar mensaje de éxito
                        $('#message').removeClass('d-none alert-danger').addClass('alert-success').html(response);
                    },
                    error: function() {
                        // Mostrar mensaje de error
                        $('#message').removeClass('d-none alert-success').addClass('alert-danger').html('Error al cambiar la contraseña.');
                    }
                });
            });
        });
    </script>
</body>
</html>
