<?php
// Incluir la conexión a la base de datos
require 'conexion.php';

if (isset($_POST['token']) && isset($_POST['password'])) {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Actualizar la contraseña y eliminar el token
    $query = $conn->prepare("UPDATE usuario SET password = :password, token = NULL WHERE token = :token");
    $query->bindParam(":password", $password);
    $query->bindParam(":token", $token);

    if ($query->execute()) {
        echo "Contraseña actualizada con éxito.";
    } else {
        echo "Error al actualizar la contraseña.";
    }
}
?>