<?php
// Asegúrate de que los datos del usuario están disponibles en la sesión
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Nombre no disponible';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Correo no disponible';
$hotel = isset($_SESSION['hotel']) ? $_SESSION['hotel'] : 'Hotel no disponible';
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'usuario'; // Si no se define, asumir rol de usuario por defecto
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium</title>

    <!-- Enlace de Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .offcanvas-header img {
            max-width: 100px; /* Ajusta el tamaño de la imagen de usuario */
            margin-top: 10px;
        }

        .offcanvas-body {
            display: flex;
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
            height: 100%; /* Asegura que el contenedor ocupe todo el espacio disponible */
        }

        .nav-tabs {
            justify-content: center; /* Asegurarse de que las pestañas también estén centradas */
        }

        .user-info {
            text-align: center;
            margin-top: 10px;
        }

        .user-info {
        font-family: 'Georgia', serif;
        font-style: italic;
        font-size: 15px;
        line-height: 1.5;
    }

    .offcanvas-body {
        font-family: 'Georgia', serif;
        font-style: italic;
    }

    .nav-link {
        font-size: 12px;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <nav class="navbar bg-body-tertiary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header text-center flex-column w-100">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel" style="font-style: italic; font-family: 'Georgia', serif; font-size: 18px;">
                Bienvenido <p><?php echo $rol; ?></p><i class="fas fa-hand-paper" style="color: yellow; margin-left: 10px;"></i>
                </h5>

                    <!-- Imagen de usuario debajo del texto -->
                    <img src="img/usuario.jpg" alt="Imagen de usuario" class="img-fluid rounded-circle">
                    <!-- Información del usuario debajo de la imagen -->
                    <div class="user-info">
                        <p><?php echo $nombre; ?></p>
                        <p><?php echo $email; ?></p>
                        <p><?php echo $hotel; ?></p>
                        
                    </div>
                    <button type="button" class="btn-close position-absolute top-0 end-0" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <!-- Menú de navegación -->
                    <ul class="nav nav-tabs">
                    <li class="nav-item">
                            <a class="nav-link active" href="./procesar_entrada_salida.html">Registro de E/S</a>
                        </li>
                        <!-- Mostrar las opciones de actualización solo si el rol es admin -->
                        <?php if ($rol === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesempleado.php">Cátalago de Empleados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesproveedor.php">Cátalago de Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesinvitado.php">Cátalago de Invitados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesusuario.php">Cátalago de Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaempleado.php"> Asistencia Empleado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciainvitado.php">Asistencia Invitado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaproveedor.php"> Asistencia Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesempresa.php">Información de la Empresa</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="offcanvas-footer">
                    <a href="logout.php" class="btn btn-outline-danger w-100">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Enlace de Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html