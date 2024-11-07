<?php
// Incluir el archivo de conexión
require '../conexion.php';

// Actualizar usuario
if (isset($_POST['actualizar'])) {
    $id_ = $_POST['id'];
    $nombre_ = $_POST['nombre'];
    $email_ = $_POST['email'];
    $hotel_ = $_POST['hotel'];
    $rol_ = $_POST['rol'];

    // Verificar si el correo ya existe en otro usuario
    $sql_check = "SELECT COUNT(*) FROM usuario WHERE email = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$email_, $id_]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        echo "<div class='alert alert-danger' role='alert'>El email ya está registrado. Por favor, ingrese un email diferente.</div>";
    } else {
        // Actualizar los datos del usuario en la base de datos
        $sql = "UPDATE usuario SET nombre = ?, email = ?, hotel = ?, rol = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$nombre_, $email_, $hotel_, $rol_, $id_])) {
            echo "<div class='alert alert-success' role='alert'>Usuario actualizado correctamente</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error al actualizar el usuario</div>";
        }
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $sql = "DELETE FROM usuario WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        echo "<div class='alert alert-success' role='alert'>Usuario eliminado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al eliminar el usuario</div>";
    }
}

// Obtener usuarios
$sql = "SELECT id, nombre, email, hotel, rol FROM usuario";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="icon" href="../img/vista.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Gestión de Usuarios</h1>

        <!-- Formulario de actualización -->
        <?php if (isset($_GET['id'])): 
            $id = $_GET['id'];
            $sql = "SELECT nombre, email, hotel, rol FROM usuario WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $usuario['email']; ?>" required>
            </div>
            <div class="mb-3">
    <label for="rol" class="form-label">Rol</label>
    <select class="form-control" id="rol" name="rol" required>
        <option value="" disabled selected>Seleccione un rol</option>
        <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Admin</option>
        <option value="usuario" <?php if ($usuario['rol'] == 'usuario') echo 'selected'; ?>>Usuario</option>
    </select>
</div>

            <div class="mb-3">
        <label for="hotel" class="form-label">Hotel</label>
        <select class="form-control" id="hotel" name="hotel" required>
        <option value="" disabled selected>Seleccione un hotel</option>
        <option value="Grand Palladium" <?php if ($usuario['hotel'] == 'Grand Palladium') echo 'selected'; ?>>Grand Palladium</option>
        <option value="TRS Coral" <?php if ($usuario['hotel'] == 'TRS Coral') echo 'selected'; ?>>TRS Coral</option>
        <option value="Transversal" <?php if ($usuario['hotel'] == 'Transversal') echo 'selected'; ?>>Transversal</option>
        </select>
        </div>

            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
        </form>
        <?php endif; ?>

        <!-- Tabla de usuarios -->
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Hotel</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['hotel']; ?></td>
                    <td><?php echo $row['rol']; ?></td>
                    <td>
                        <a href="actualizacionesusuario.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="actualizacionesusuario.php?eliminar=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Contenedor para los botones -->
<div class="text-center mt-4">
    <!-- Botón para volver -->
    <button class="btn btn-secondary me-2" onclick="history.back()">Volver</button>
    <!-- Botón para actualizar -->
    <button class="btn btn-primary" onclick="location.reload()">Actualizar</button>
</div>


    <!-- Enlaces de Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
