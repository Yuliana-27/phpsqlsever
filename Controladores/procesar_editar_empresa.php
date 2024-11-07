<?php
require_once '../conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empresa = $_POST['id_empresa'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $ubicacion = $_POST['ubicacion'];
    $ruc = $_POST['ruc'];

    try {
        // Preparar y ejecutar la consulta de actualización
        $stmt = $conn->prepare("UPDATE empresa SET nombre = :nombre, telefono = :telefono, ubicacion = :ubicacion, ruc = :ruc WHERE id_empresa = :id_empresa");
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':ubicacion' => $ubicacion,
            ':ruc' => $ruc,
            ':id_empresa' => $id_empresa
        ]);

        if ($stmt->rowCount() > 0) {
            echo "Información actualizada exitosamente.";
        } else {
            echo "No se realizaron cambios. Verifica los datos ingresados.";
        }
    } catch (PDOException $e) {
        echo "Error en la actualización: " . $e->getMessage();
    }
}
?>
