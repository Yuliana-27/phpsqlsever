<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium Hotel Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/vista.png" type="image/png">

    <style>
        body {
            background-color: aliceblue;
        }
    </style>

</head>
<body>
<?php
    // Incluir la conexión
    require 'conexion.php';
    ?>

    <div class="container w-50 mt-5 shadow p-5 bg-transparent rounded text-center">
        <h2 class="fw-bold text-center py-4">REGISTRO DE USUARIO</h2>

        <form class="mx-auto w-75" >

        <div class="mb-3">
                <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
                <input type="text" class="form-control" id="nombreRegistro" name="nombre" required>
            </div>
        
            <div class="mb-3">
                <label for="hotel" class="form-label">Complejo</label>
                <select class="form-select" id="hotelRegistro" name="hotel" required>
                    <option value="" disabled selected>Selecciona tu complejo</option>
                    <option value="grandpalladium">Grand Palladium</option>
                    <option value="trscoral">TRS Coral</option>
                    <option value="transversal">Transversal</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" class="form-control" id="emailRegistro" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Contraseña</label>
                <input type="password" class="form-control" id="passwordRegistro" name="password" required>
            </div>

            <!-- Nuevo campo para seleccionar el rol -->
            <div class="mb-3">
    <label for="rol" class="form-label fw-bold">Rol</label>
    <select class="form-select" id="rolRegistro" name="rol" required>
        <option value="" disabled selected>Selecciona el rol</option>
        <option value="admin">Admin</option>
        <option value="usuario">Usuario</option>
    </select>
</div>



            <div class="d-grid">
                <button type="button" onclick="registrar()" class="btn btn-success">Registrar</button>
            </div>

            <div class="my-3">
                <span>¿Ya tienes cuenta? <a href="index.php">Iniciar Sesión</a></span>
            </div>
        </form>
    </div>

    <script src="registro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>