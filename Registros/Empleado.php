<?php
require '../conexion.php';
require '../phpqrcode/qrlib.php';
require '../Email/Exception.php';
require '../Email/PHPMailer.php';
require '../Email/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    $email = $_POST['email']; // Correo electrónico al que se enviará el QR

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
    $contenidoQR = "empleado|$numeroColaborador|\n$nombre|\n$area|\n$placas|\n$modeloMarca|$color";
    $filename = "../img_qr/qr_" . $numeroColaborador . ".png";

    QRcode::png($contenidoQR, $filename, QR_ECLEVEL_L, 4);

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
        // Enviar el correo con el QR adjunto
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'isc20350250@gmail.com';  // Tu correo
            $mail->Password   = 'bwfzbxzrscmwgzmx';             // Contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente y destinatario
            $mail->setFrom('isc20350250@gmail.com', 'Yuliana del Carmen Altamirano Montes');
            $mail->addAddress($email, $nombre);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Tu Codigo QR de Acceso';
            $mail->Body = "Estimado/a $nombre,<br><br>

    
                Se le envía su código QR para poder entrar a las instalaciones del Hotel Palladium.<br><br> 
                <b>Formulario de Invitación:</b>
                Puede Enviar Este Link  A Las Personas Que Quiera Invitar<br><br>
                Una vez que hayamos recibido su información, le enviaremos su código QR para acceder a nuestras instalaciones<br><br>
                https://docs.google.com/forms/d/e/1FAIpQLSc2OHUEju_tsaLvo8Aj-GoAmI7j_rvtei0tT_VKyV9pNyo8pw/viewform?usp=sf_link";
                

            $mail->AltBody = "Estimado/a $nombre, para completar tu registro y recibir tu pase de entrada, llena el siguiente formulario:\n
                            Formulario de Invitación: https://forms.gle/tu-enlace-aqui\n
                            Una vez que hayamos recibido tu información, te enviaremos tu código QR.";



            // Adjuntar el código QR
            $mail->addAttachment($filename,'CodigoQR.png');

            // Enviar el correo
            $mail->send();

            $response["status"] = "success";
            $response["message"] = "Empleado registrado con éxito. El QR ha sido enviado por correo.";
            $response["qr_code_url"] = "img_qr/qr_" . $numeroColaborador . ".png";
        } catch (Exception $e) {
            $response['message'] = 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
        }
    } else {
        $response['message'] = "Error al registrar: " . implode(" - ", $stmt->errorInfo());
    }
}

echo json_encode($response);
?>
