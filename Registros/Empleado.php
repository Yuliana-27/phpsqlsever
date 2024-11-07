<?php
require '../conexion.php';
require '../phpqrcode/qrlib.php';

header('Content-Type: application/json'); // Configurar para respuesta JSON

$response = [
    "status" => "error",
    "message" => "Ocurrió un error desconocido."
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $numeroColaborador = $_POST['numero_colaborador'];
    $area = $_POST['area'];
    $placas = $_POST['placas'];
    $modeloMarca = $_POST['modelo_marca'];
    $color = $_POST['color'];

    // Verificar si las placas ya están registradas en alguna de las tablas: empleados, invitados o proveedores
    $verificar_placas_empleados = $conn->prepare("SELECT * FROM empleados WHERE placas_vehiculo = :placas");
    $verificar_placas_invitados = $conn->prepare("SELECT * FROM invitados WHERE placas_vehiculo = :placas");
    $verificar_placas_proveedores = $conn->prepare("SELECT * FROM proveedores WHERE placas_vehiculos = :placas");
    
    // Vincular el parámetro de las placas
    $verificar_placas_empleados->bindParam(':placas', $placas);
    $verificar_placas_invitados->bindParam(':placas', $placas);
    $verificar_placas_proveedores->bindParam(':placas', $placas);

    // Ejecutar las consultas
    $verificar_placas_empleados->execute();
    $verificar_placas_invitados->execute();
    $verificar_placas_proveedores->execute();

    // Verificar si hay algún registro que coincida, se agregó un fetch para verificar el registro de placas
    $placa_existe_en_empleados = $verificar_placas_empleados->fetch(); 
    $placa_existe_en_invitados = $verificar_placas_invitados->fetch();
    $placa_existe_en_proveedores = $verificar_placas_proveedores->fetch();

    // Si las placas ya existen en alguna de las tablas, mostrar error
    if ($placa_existe_en_empleados || $placa_existe_en_invitados || $placa_existe_en_proveedores) {
        $response['message'] = "Error: Ya existe un registro con esas placas en empleados, invitados o proveedores.";
        echo json_encode($response);
        exit;
    }

    // Generar el contenido del código QR siempre y cuando no existan las placas
    $contenidoQR = "empleado $numeroColaborador \n$nombre \n$area \n$placas \n$modeloMarca ($color)";
    $filename = "../img_qr/qr_" . $numeroColaborador . ".png";

    QRcode::png($contenidoQR, $filename, QR_ECLEVEL_L, 4);

    // Preparar la respuesta con la URL del código QR para mostrar y descargar
$response = [
    "status" => "success",
    "message" => "Empleado registrado con éxito.",
    "qr_code_url" => "img_qr/qr_" . $numeroColaborador . ".png"  // Asegúrate de que la ruta sea correcta
];


    // Insertar en la base de datos
    $sql = "INSERT INTO empleados (nombre_apellido, numero_colaborador, area, placas_vehiculo, modelo_marca, color_vehiculo, qr_code) 
            VALUES (:nombre, :numeroColaborador, :area, :placas, :modeloMarca, :color, :qr_code)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':numeroColaborador', $numeroColaborador);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':placas', $placas);
    $stmt->bindParam(':modeloMarca', $modeloMarca);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':qr_code', $filename);

    if ($stmt->execute()) {
        $response["status"] = "success";
        $response["message"] = "Empleado registrado con éxito.";
    } else {
        $response['message'] = "Error al registrar: " . implode(" - ", $stmt->errorInfo());
    }
}

echo json_encode($response);
?>
