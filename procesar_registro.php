<?php
ini_set('display_errors', 1);
error_reporting(E_ALL); // Para la depuración

require 'conexion.php';
header('Content-Type: application/json');

// Obtener los datos enviados en formato JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verificar si todos los campos requeridos están presentes
if (!isset($input['nombre'], $input['hotel'], $input['email'], $input['password'], $input['rol'])) {
    echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
    exit;
}

$nombre = $input['nombre'];
$hotel = $input['hotel'];
$email = $input['email'];
$password = $input['password'];
$rol = $input['rol']; // Obtener el rol

// Validar si los campos están vacíos
if (empty($nombre) || empty($hotel) || empty($email) || empty($password) || empty($rol)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
    exit;
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido']);
    exit;
}

try {
    // Comprobar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Este correo ya está registrado']);
        exit;
    }

    // Encriptar la contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insertar los datos en la base de datos, incluyendo el rol
    $stmt = $conn->prepare("INSERT INTO usuario (nombre, hotel, email, password, rol) VALUES (:nombre, :hotel, :email, :password, :rol)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':hotel', $hotel);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':rol', $rol); // Vincular el rol

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el registro: ' . $e->getMessage()]);
}
?>
