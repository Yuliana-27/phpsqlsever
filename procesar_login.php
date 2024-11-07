<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
session_start(); // Asegúrate de que la sesión está iniciada
header('Content-Type: application/json');

try {
    // Obtener los datos JSON del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Error en el formato del JSON recibido.']);
        exit;
    }

    if (!isset($input['email']) || !isset($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
        exit;
    }

    $email = $input['email'];
    $password = $input['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
        exit;
    }

    // Verificar si el correo está registrado
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Almacenar los datos del usuario en la sesión
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['hotel'] = $user['hotel'];
        $_SESSION['rol'] = $user['rol'];  // Almacenar el rol

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?>

