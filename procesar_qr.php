<?php
header('Content-Type: application/json');
// Conectar a la base de datos
require 'conexion.php';

// Definir la zona horaria de Cancún
date_default_timezone_set('America/Cancun');
$fecha = date('Y-m-d H:i:s');
$response = ['success' => false, 'message' => ''];

// Verificar si se ha escaneado el código QR y se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del QR y la acción
    $qrData = $_POST['qr_data'] ?? null; // Validar si existe el contenido del QR
    $action = $_POST['action'] ?? null;  // Validar si existe la acción (entrada o salida)

    // Verificar que los datos esenciales existan
    if ($qrData && $action && in_array($action, ['entrada', 'salida'])) {
        
        // Intentar descomponer el contenido del QR (si el formato es esperado)
        $qrParts = explode(' ', $qrData);
        
        // Verificar que el QR tenga el formato correcto (mínimo 1 parte: número de colaborador, proveedor o invitado)
        if (count($qrParts) > 1) {
            $tipo = trim($qrParts[0]); // Tipo de usuario
            $identificador = trim($qrParts[1]); // Número de colaborador, proveedor o nombre

            // Verificar si el QR ha expirado según el tipo de usuario
            $fechaExpiracion = null;
            if ($tipo == 'proveedor') {
                $sql = "SELECT fecha_expiracion FROM proveedores WHERE proveedor = :identificador";
            } elseif ($tipo == 'invitado') {
                $sql = "SELECT fecha_expiracion FROM invitados WHERE nombre_apellido = :identificador";
            } else {
                $fechaExpiracion = null; // Los empleados tienen QR permanente, no se verifica la expiración
            }

            // Solo ejecutar la consulta de expiración si es un invitado o proveedor
            if ($fechaExpiracion !== null) {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':identificador', $identificador);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $fechaExpiracion = $result['fecha_expiracion'];
                    if ($fechaExpiracion && new DateTime($fecha) > new DateTime($fechaExpiracion)) {
                        // El QR ha expirado
                        $response['message'] = "El código QR ha vencido.";
                        echo json_encode($response);
                        exit;
                    }
                } else {
                    $response['message'] = "No se encontró el registro en la base de datos.";
                    echo json_encode($response);
                    exit;
                }
            }

            // Comenzar una transacción para garantizar que las operaciones se realicen de manera atómica
            $conn->beginTransaction();

            try {
                // --- Registro en tabla asistencia_empleados ---
                if ($tipo == 'empleado') {
                    // Buscar si ya existe un registro de entrada sin salida
                    if ($action == 'entrada') {
                        $sqlInsert = "INSERT INTO asistencia_empleado (numero_colaborador, fecha_entrada) VALUES (:identificador, :fecha)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':identificador', $identificador);
                        $stmtInsert->bindParam(':fecha', $fecha);
                        $stmtInsert->execute();

                     // Si no existe registro de salida, registrar nueva entrada
                    } elseif ($action == 'salida') {
                    // Actualizar la fecha de salida
                        $sqlSalida = "UPDATE asistencia_empleado SET fecha_salida = :fecha WHERE numero_colaborador = :identificador AND fecha_salida IS NULL";
                        $stmtSalida = $conn->prepare($sqlSalida);
                        $stmtSalida->bindParam(':fecha', $fecha);
                        $stmtSalida->bindParam(':identificador', $identificador);
                        $stmtSalida->execute();
                    }
                }

                // --- Registro en tabla asistencia_proveedores ---
                elseif ($tipo == 'proveedor') {
                    if ($action == 'entrada') {
                        $sqlInsert = "INSERT INTO asistencia_proveedor (proveedor, fecha_entrada) VALUES (:identificador, :fecha)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':identificador', $identificador);
                        $stmtInsert->bindParam(':fecha', $fecha);
                        $stmtInsert->execute();

                    } elseif ($action == 'salida') {
                        $sqlSalida = "UPDATE asistencia_proveedor SET fecha_salida = :fecha WHERE proveedor = :identificador AND fecha_salida IS NULL";
                        $stmtSalida = $conn->prepare($sqlSalida);
                        $stmtSalida->bindParam(':fecha', $fecha);
                        $stmtSalida->bindParam(':identificador', $identificador);
                        $stmtSalida->execute();
                    }
                }

                // --- Registro en tabla asistencia_invitados ---
                elseif ($tipo == 'invitado') {
                    if ($action == 'entrada') {
                        $sqlInsert = "INSERT INTO asistencia_invitado (nombre_apellido, fecha_entrada) VALUES (:identificador, :fecha)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert->bindParam(':identificador', $identificador);
                        $stmtInsert->bindParam(':fecha', $fecha);
                        $stmtInsert->execute();

                    } elseif ($action == 'salida') {
                        $sqlSalida = "UPDATE asistencia_invitado SET fecha_salida = :fecha WHERE nombre_apellido = :identificador AND fecha_salida IS NULL";
                        $stmtSalida = $conn->prepare($sqlSalida);
                        $stmtSalida->bindParam(':fecha', $fecha);
                        $stmtSalida->bindParam(':identificador', $identificador);
                        $stmtSalida->execute();
                    }
                }

                // Si todas las operaciones fueron exitosas, confirmar la transacción
                $conn->commit();
                $response['success'] = true;
                $response['message'] = "Registro actualizado correctamente.";

            } catch (Exception $e) {
                // En caso de error, deshacer la transacción
                $conn->rollBack();
                $response['message'] = "Ocurrió un error: " . $e->getMessage();
            }
        } else {
            $response['message'] = "El formato del QR no es válido.";
        }
    } else {
        $response['message'] = "Datos incompletos.";
    }
}
echo json_encode($response);
?>