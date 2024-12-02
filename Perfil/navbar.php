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
    /* Ajustes generales */
    .offcanvas-header img {
        max-width: 80px; /* Reduce el tamaño para móviles */
        margin-top: 10px;
    }

    .user-info {
        font-family: 'Georgia', serif;
        font-style: italic;
        font-size: 14px;
        line-height: 1.5;
    }

    .offcanvas-body {
        font-family: 'Georgia', serif;
        font-style: italic;
        overflow-y: auto; /* Habilita desplazamiento vertical */
        max-height: 50vh; /* Ajuste dinámico para pantallas pequeñas */
    }

     /* Estilo general para nav-tabs */
      .nav-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); /* Distribuir en columnas flexibles */
        grid-auto-rows: auto; /* Asegura que las filas se ajusten automáticamente */
        gap: 10px; /* Espaciado entre elementos */
        justify-content: center; /* Centra los elementos en el contenedor */
    }

     /* Estilo general para los elementos de las pestañas */
    .nav-item {
        text-align: center; /* Centra el contenido */
        padding: 5px; /* Espaciado interno */
    }


    .nav-link {
        font-size: 14px; /* Tamaño dinámico */
        font-weight: bold;
        text-align: center;
    }

    .nav-item i {
        font-size: 1.2rem; /* Ajusta el tamaño de los íconos */
        margin-right: 5px; /* Espacio entre ícono y texto */
    }

     /* Media query para pantallas grandes: 3 columnas */
     @media (min-width: 768px) {
        .nav-tabs {
            grid-template-columns: repeat(3, 1fr); /* Cambia a cuatro columnas */
        }
    }

    /* Media query para pantallas pequeñas: 2 filas */
    @media (max-width: 767px) {
        .nav-tabs {
            grid-template-columns: repeat(2, 1fr); /* Mantiene dos columnas */
            grid-auto-rows: auto; /* Asegura el ajuste dinámico de filas */
        }
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
                    <?php if ($rol === 'operador'): ?>
                    <li class="nav-item">
                            <a class="nav-link active" href="./procesar_entrada_salida.html" target="_blank">
                            <i class="bi bi-journal"></i>Registro de E/S</a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaempleado.php">
                            <i class="bi bi-journal-check"></i>Asistencia Empleado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaproveedor.php">
                            <i class="bi bi-journal-check"></i>Asistencia Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciainvitado.php">
                            <i class="bi bi-journal-check"></i>Asistencia Invitado</a>
                        </li>

                    <?php elseif ($rol === 'usuario'): ?>

                        <li class="nav-item">
                            <a class="nav-link active" href="./procesar_entrada_salida.html" target="_blank">
                            <i class="bi bi-journal"></i>Registro de E/S</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesempleado.php">
                            <i class="bi bi-book"></i>Cátalago de Empleados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesproveedor.php">
                            <i class="bi bi-book"></i>Cátalago de Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesinvitado.php">
                            <i class="bi bi-book"></i>Cátalago de Invitados</a>
                        </li>
                        
                        <!-- Mostrar las opciones de actualización solo si el rol es admin -->
                        <?php elseif ($rol === 'admin'): ?>
                            <li class="nav-item">
                            <a class="nav-link active" href="./procesar_entrada_salida.html" target="_blank">
                            <i class="bi bi-journal"></i>Registro de E/S</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesusuario.php">
                            <i class="bi bi-book"></i>Cátalago de Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesempresa.php">
                            <i class="bi bi-info-circle"></i>Información de la Empresa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesempleado.php">
                            <i class="bi bi-book"></i>Cátalago de Empleados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesinvitado.php">
                            <i class="bi bi-book"></i>Cátalago de Invitados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/actualizacionesproveedor.php">
                            <i class="bi bi-book"></i>Cátalago de Proveedores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaempleado.php">
                            <i class="bi bi-journal-check"></i>Asistencia Empleado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciainvitado.php">
                            <i class="bi bi-journal-check"></i>Asistencia Invitado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="./Controladores/asistenciaproveedor.php">
                            <i class="bi bi-journal-check"></i>Asistencia Proveedores</a>
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