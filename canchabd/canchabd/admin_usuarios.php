<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener solo admins y empleados, incluyendo el teléfono
$sql = "SELECT usuarios.*, roles.nombre AS rol_nombre FROM usuarios 
        JOIN roles ON usuarios.rol_id = roles.id 
        WHERE roles.nombre IN ('admin', 'empleado')";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_admin.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

    <div class="container mt-5">
        <h1 class="text-center mb-4 text-white">👥 Gestionar Usuarios</h1>

        <div class="card shadow-lg">
            <div class="card-body" style="background-color: rgba(255, 255, 255, 0.8); border-radius: 10px;">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th> <!-- Nueva columna para el teléfono -->
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['telefono']); ?></td> <!-- Mostrar el número de teléfono -->
                            <td><?php echo ucfirst($user['rol_nombre']); ?></td>
                            <td><?php echo ucfirst($user['estado']); ?></td>
                            <td>
                                <!-- Bloquear / Desbloquear -->
                                <?php if ($user['estado'] == 'activo') { ?>
                                    <a href="bloquear_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Bloquear</a>
                                <?php } else { ?>
                                    <a href="desbloquear_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm">Desbloquear</a>
                                <?php } ?>
                                <!-- Editar -->
                                <a href="editar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">✏️ Editar</a>
                                
                                <!-- Eliminar -->
                                <a href="eliminar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón para crear un nuevo usuario -->
        <div class="text-center mt-3">
            <a href="crear_usuario.php" class="btn btn-primary btn-lg">➕ Crear Usuario</a>
            <a href="admin_panel.php" class="btn btn-secondary btn-lg">⬅ Volver</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
