<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palladium Hotel Group</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/vista.png" type="image/png">

<style>
    body {
        background-color: aliceblue;
    }
</style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Recuperación de Contraseña</h2>

        <?php
        // Incluir la conexión a la base de datos
        require 'conexion.php';

        // Cargar PHPMailer
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;

        require 'Email/Exception.php';
        require 'Email/PHPMailer.php';
        require 'Email/SMTP.php';

        if (isset($_POST['email'])) {
            $email = $_POST['email'];

            // Mostrar el correo recibido
            echo '<div class="alert alert-info"><strong>Correo recibido:</strong> ' . htmlspecialchars($email) . '</div>';

            // Verificar si el correo existe en la base de datos
            $query = $conn->prepare("SELECT id FROM usuario WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))");
            $query->bindParam(":email", $email);
            $query->execute();
            
            // Mostrar el resultado de la consulta
            $result = $query->fetchAll();
            echo '<pre class="alert alert-secondary"><strong>Resultado de la consulta:</strong><br>';
            print_r($result);
            echo '</pre>';

            if ($query->rowCount() > 0) {
                // Generar token único
                $token = bin2hex(random_bytes(50));

                // Mostrar el token generado
              //  echo '<div class="alert alert-success"><strong>Token generado:</strong> ' . htmlspecialchars($token) . '</div>';

                // Guardar el token en la base de datos
                $query = $conn->prepare("UPDATE usuario SET token = :token WHERE email = :email");
                $query->bindParam(":token", $token);
                $query->bindParam(":email", $email);
                $query->execute();

                // Crear enlace de recuperación
                $link = "http://localhost:8080/PHPSQLSever/restablecer_contrasena.php?token=" . $token;

                // Mostrar el enlace generado
              //  echo '<div class="alert alert-warning"><strong>Enlace generado:</strong> <a href="' . htmlspecialchars($link) . '" target="_blank">' . htmlspecialchars($link) . '</a></div>';

                // Enviar el correo
                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'isc20350250@gmail.com';  // Coloca tu email
                    $mail->Password   = 'bwfzbxzrscmwgzmx';       // Coloca tu contraseña de aplicación
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Destinatarios
                    $mail->setFrom('isc20350250@gmail.com', 'Yuliana del Carmen Altamirano Montes');
                    $mail->addAddress($email);  // Enviar el correo al destinatario

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperacion de contrasena';
                    $mail->Body    = 'Haz clic en el siguiente enlace para restablecer tu contraseña: <a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a>';

                    // Enviar el correo
                    $mail->send();
                    echo '<div class="alert alert-success">Revisa tu correo electrónico para restablecer tu contraseña.</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Error al enviar el correo: ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
                }
            } else {
                echo '<div class="alert alert-danger">El correo electrónico no está registrado.</div>';
            }
        }
        ?>

    </div>

    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
