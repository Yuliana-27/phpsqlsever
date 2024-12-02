<?php
session_start();
$rol = $_SESSION['rol']; // Asegúrate de guardar el rol cuando el usuario inicie sesión

if ($rol === 'admin') {
    // Mostrar solo la opción de registro
    echo '<a href="panelqr.php">Agregar</a>';
    echo '<a href="actualizacionesempleado.php"></a>';
    echo '<a href="actualizacionesinvitado.php"></a>';
    echo '<a href="actualizacionesproveedor.php"></a>';
    echo '<a href="actualizacionesusuario.php"></a>';
    echo '<a href="asistenciaempleado.php"></a>';
    echo '<a href="asistenciainvitado.php"></a>';
    echo '<a href="asistenciaproveedor.php"></a>';
    echo '<a href="procesar_entrada_salida.html"></a>';
} elseif ($rol === 'operador') {
    // Mostrar todas las opciones
    echo '<a href="procesar_entrada_salida.html"></a>';
} elseif ($rol === 'usuario') {
    // Mostrar solo la opción de agregar invitados y proveedores
    echo '<a href="panelqr.php"></a>';

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium Hotel Group</title>
    <link rel="icon" href="img/vista.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: aliceblue;
        }
        h4 {
            font-family: 'Georgia', serif;
        font-style: italic;
        font-size: 14px;
        line-height: 1.5;
        }

        .container {  
        padding-top: 10px; /* Reducir el espacio superior */
    }

    </style>
</head>
<body>
    <?php
    ob_start();
    if(!isset($_SESSION["nombre"])) {
        header("Location: index.php");
    } else
    // Incluir la conexión a la base de datos
    require 'conexion.php';
    require 'phpqrcode/qrlib.php'; // Librería PHP QR Code
    require './Perfil/navbar.php'
    ?>

     <!-- Contenedor Principal -->
     <div class="container mt-5">
        <!-- Logos -->
        <div class="row justify-content-center mb-5">
            <div class="col-3 col-md-1 text-center">
                <img src="img/Palladium.png" class="img-fluid" alt="Logo Palladium">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/gp_hotels.png" class="img-fluid" alt="Logo GP Hotels">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/TRS.png" class="img-fluid" alt="Logo TRS">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/ayre.png" class="img-fluid" alt="Logo Ayre">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/fiesta.png" class="img-fluid" alt="Logo Fiesta">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/oy-logo.png" class="img-fluid" alt="Logo OY">
            </div>
            <div class="col-3 col-md-1 text-center">
                <img src="img/pbh-logo.png" class="img-fluid" alt="Logo PBH">
            </div>
        </div>

        <!-- Panel de Registro -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 shadow p-4 bg-white rounded">
                <?php if ($rol !== 'operador'): ?>
                    <h2 class="text-center mb-4"><strong>Panel de Registro</strong></h2>
                    <div class="text-center mb-3">
                        <h4>Seleccione el tipo de usuario a registrar:</h4>
                        <div class="btn-group d-flex" role="group" aria-label="Opciones de Registro">
                            <button class="btn btn-outline-primary w-100" id="btnEmpleado">Empleado</button>
                            <button class="btn btn-outline-secondary w-100" id="btnInvitado">Invitado</button>
                            <button class="btn btn-outline-success w-100" id="btnProveedor">Proveedor</button>
                        </div>
                

        <div id="formContainer" class="p-4 shadow rounded bg-white"></div>

    <?php else: ?>
        <!-- Mensaje alternativo para el operador -->
        
        <?php endif; ?>
        </div>
    <!-- Script para manejar los formularios -->
    <script>

        const btnEmpleado = document.getElementById('btnEmpleado');
        const btnInvitado = document.getElementById('btnInvitado');
        const btnProveedor = document.getElementById('btnProveedor');
        const formContainer = document.getElementById('formContainer');

        // Limpiar el contenedor del formulario
        function clearForm() {
            formContainer.innerHTML = '';
        }

        // Función para enviar el formulario de registro de empleado a través de AJAX
        function sendFormData(url, formId) {
    const formData = new FormData(document.getElementById(formId));
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            let downloadFileName;

            //Asignar el nombre del archivo segun el tipo de formulario
            if (formId ==="registroEmpleadoForm"){
                downloadFileName = `qr_${formData.get("numero_colaborador")}.png`;
            } else if (formId === "registroInvitadoForm"){
                downloadFileName = `qr_${formData.get("nombre")}.png`;
            } else if (formId === "registroProveedorForm"){
                downloadFileName = `qr_${formData.get("proveedor")}.png`;
            }

            formContainer.innerHTML = `
                <div class="alert alert-success text-center">${data.message}</div>
                <div class="text-center">
                    <img src="${data.qr_code_url}" alt="Código QR generado" style="max-width: 200px; margin: 20px 0;">
                    <a href="${data.qr_code_url}" download="CodigoQR_${downloadFileName}" class="btn btn-success">Descargar Código QR</a>
                </div>`;
        } else {
            formContainer.innerHTML = `<div class="alert alert-danger text-center">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error("Error en el envío del formulario:", error);
        formContainer.innerHTML = "<div class='alert alert-danger'>Ocurrió un error al enviar el formulario.</div>";
    });
}

        // Formulario de registro de empleados
        function formEmpleado() {
            clearForm();
            formContainer.innerHTML = `
                <h4>Registro de Empleado</h4>
                <form id="registroEmpleadoForm">

                <input type="hidden" name="tipo" value="empleado">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre y Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="numero_colaborador" class="form-label">Número de Colaborador <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="numero_colaborador" name="numero_colaborador" required>
                    </div>
                    <div class="mb-3">
                        <label for="area" class="form-label">Departamento</label>
                        <select class="form-select" id="area" name="area" required>
                        <option value="" disabled selected>Selecciona un Área</option>
                        <option value="RRHH">RRHH</option>
                        <option value="Servicios Técnicos">Servicios Técnicos</option>
                        <option value="Lavanderia">Lavanderia</option>
                        <option value="Roperia">Roperia</option>
                        <option value="Bodas">Bodas</option>
                        <option value="Entretenimiento">Entretenimiento</option>
                        <option value="Servibar">Servibar</option>
                        <option value="Travel Club">Travel Club</option>
                        <option value="Spa">Spa</option>
                        <option value="It">It</option>
                        <option value="Recepción">Recepción</option>
                        <option value="Redes Sociales">Redes Sociales</option>
                        <option value="Butler">Butler</option>
                        <option value="Seguridad">Seguridad</option>
                        <option value="Room Service">Room Service</option>
                        <option value="Palladium Rewards">Palladium Rewards</option>
                    </select>
                    </div>

                    <div class="mb-3">
                    <label for="tipo_Qr" class="form-label">Tipo de Qr <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipo_Qr" name="tipo_Qr" required>
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="permanente">Permanente</option>
                            </select>
                    </div>

                    <div class="mb-3">
                        <label for="placas" class="form-label">Placas del Vehículo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="placas" name="placas" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo_marca" class="form-label">Modelo y Marca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modelo_marca" name="modelo_marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color del Vehículo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="color" name="color" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Enviar Codigo Qr al Correo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="sendFormData('Registros/Empleado.php', 'registroEmpleadoForm')" >Registrar Empleado</button>
                </form>
            `;
        }

        // Formulario de registro de invitados
        function formInvitado() {
            clearForm();
            formContainer.innerHTML = `
                <h4>Registro de Invitado</h4>
                <form id="registroInvitadoForm">

                    <input type="hidden" name="tipo" value="invitado">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre y Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="area_asiste" class="form-label">Área a la que Asiste <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="area_asiste" name="area_asiste" required>
                    </div>

                    <div class="mb-3">
                    <label for="duracion" class="form-label">Duración Del Qr<span class="text-danger">*</span></label>
                        <select class="form-control" id="duracion" name="duracion" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="3">3 días</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="placas" class="form-label">Placas del Vehículo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="placas" name="placas" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo_marca" class="form-label">Modelo y Marca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modelo_marca" name="modelo_marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color del Vehículo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="color" name="color" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Enviar Codigo Qr al Correo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="sendFormData('Registros/Invitado.php', 'registroInvitadoForm')">Registrar Invitado</button>
                </form>
            `;
        }

        // Formulario de registro de proveedores
        function formProveedor() {
            clearForm();
            formContainer.innerHTML = `
                <h4>Registro de Proveedor</h4>
                <form id="registroProveedorForm">

                    <input type="hidden" name="tipo" value="proveedor">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre y Apellido</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="proveedor" class="form-label">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                    </div>

                    <div class="mb-3">
                    <label for="duracion" class="form-label">Duración Del Qr<span class="text-danger">*</span></label>
                        <select class="form-control" id="duracion" name="duracion" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="3">3 días</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="placas" class="form-label">Placas del Vehículo</label>
                        <input type="text" class="form-control" id="placas" name="placas" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo_marca" class="form-label">Modelo y Marca</label>
                        <input type="text" class="form-control" id="modelo_marca" name="modelo_marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color del Vehículo</label>
                        <input type="text" class="form-control" id="color" name="color" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Enviar Codigo Qr al Correo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="button" class="btn btn-success" onclick="sendFormData('Registros/Proveedores.php', 'registroProveedorForm')">Registrar Proveedor</button>
                </form>
            `;
        }
    

        // Asignar eventos a los botones
        btnEmpleado.addEventListener('click', formEmpleado);
        // Rol usuario: Solo habilitamos formularios de invitado y proveedor
if (btnInvitado) {
    btnInvitado.addEventListener('click', formInvitado);
}
if (btnProveedor) {
    btnProveedor.addEventListener('click', formProveedor);
}

    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<?php}
ob_end_flush();
?>