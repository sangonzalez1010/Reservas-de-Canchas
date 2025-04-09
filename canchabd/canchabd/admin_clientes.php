<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener clientes
$sql = "SELECT usuarios.*, roles.nombre AS rol_nombre FROM usuarios 
        JOIN roles ON usuarios.rol_id = roles.id 
        WHERE roles.nombre = 'Cliente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_clientes.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

    <div class="container mt-5">
        <h1 class="text-center mb-4 text-white">👥 Gestionar Clientes</h1>

        <div class="card shadow-lg">
            <div class="card-body" style="background-color: rgba(255, 255, 255, 0.8); border-radius: 10px;">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cliente = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($cliente['rol_nombre'])); ?></td>
                            <td><?php echo ($cliente['estado'] == 'activo') ? 'Activo' : 'Bloqueado'; ?></td>
                            <td>
                                <!-- Botón de Editar (Solo para Clientes) -->
                                <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-info btn-sm">✏️ Editar</a>

                                <!-- Botón de Bloquear / Desbloquear -->
                                <?php if ($cliente['estado'] == 'activo') { ?>
                                    <a href="bloquear_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning btn-sm">🚫 Bloquear</a>
                                <?php } else { ?>
                                    <a href="desbloquear_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-success btn-sm">✅ Desbloquear</a>
                                <?php } ?>

                                <!-- Botón de Eliminar -->
                                <a href="eliminar_clientes.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cliente?');">🗑️ Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón de Regresar -->
        <div class="text-center mt-3">
            <a href="admin_panel.php" class="btn btn-secondary">⬅️ Regresar</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
