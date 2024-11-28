<!DOCTYPE html>
<html lang="en">
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
        .logo-container img {
            max-width: 50px;
            margin: 5px;
        }
        footer {
            text-align: center; 
            padding: 15px; 
            background: linear-gradient(90deg, #4b79a1, #283e51); 
            color: white; 
            font-family: Arial, sans-serif; 
            font-size: 14px; 
            border-top: 3px solid #ffdd57;
        }
        footer a {
            color: #ffdd57; 
            text-decoration: none;
        }
    </style>
    
    <script src="login.js"></script>
</head>
<body>
    <?php
    // Incluir la conexión
    require 'conexion.php';
    ?>
    <!-- Contenedor Principal -->
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <!-- Logo (Con diseño responsivo) -->
        <div class="logo-container text-center mb-4 d-flex flex-wrap justify-content-center">
            <img src="img/Palladium.png" alt="Palladium">
            <img src="img/gp_hotels.png" alt="GP Hotels">
            <img src="img/TRS.png" alt="TRS">
            <img src="img/ayre.png" alt="Ayre">
            <img src="img/fiesta.png" alt="Fiesta">
            <img src="img/hardrock.jpg" alt="Hard Rock">
            <img src="img/oy-logo.png" alt="Only You">
            <img src="img/ushuaia.png" alt="Ushuaia">
            <img src="img/pbh-logo.png" alt="PBH">
        </div>

        <!-- Título -->
        <h2 class="fw-bold text-center py-4">BIENVENIDO</h2>

        <!-- Formulario -->
        <div class="w-100" style="max-width: 400px;">
            <form action="#" class="mx-auto">
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" class="form-control" name="email" id="email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="pass">
                </div>

                <div class="d-grid">
                    <button type="button" onclick="loguear()" class="btn btn-danger">Iniciar Sesión</button>
                </div>

                <div class="my-3 text-center">
                    <span>¿No tienes cuenta? <a href="RegistroUsuario.php">Regístrate</a></span> <br>
                    <span><a href="RecuperarContraseña.php" target="_blank">Recuperar Contraseña</a></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p style="margin: 0;">
            © <?php echo date('Y'); ?> <strong>Yuliana del Carmen & Gerardo Luis</strong>. Todos los derechos reservados.
        </p>
        <p style="margin: 5px 0 0;">
            <a href="terminos.php">Términos de uso</a> |
            <a href="politica_privacidad.php">Política de privacidad</a>
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
