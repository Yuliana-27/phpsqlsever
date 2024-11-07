<?php

$server = 'YULIANA\SQLEXPRESS';
$database = 'vehiculos_db(1)';
$username = 'sa';
$password = '12345678';
$port = '1433';

try {
    // Crear la conexión con la base de datos
    $conn = new PDO("sqlsrv:Server=$server;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa"; // Esta línea se comenta o se elimina para no mostrar el mensaje
    // Aquí se pueden realizar operaciones con la base de datos
} catch (PDOException $e) {
    // En caso de error, mostrar el mensaje de error
    echo "Error de conexión: " . $e->getMessage();
}

?>
