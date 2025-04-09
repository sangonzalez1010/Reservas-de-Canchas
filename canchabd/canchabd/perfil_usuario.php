<?php
session_start();
include 'config.php';  // Conexión a la base de datos

// Activar la visualización de errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";

// Obtener datos del usuario
$sql_usuario = "SELECT nombre, email, telefono FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_datos'])) {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_email = trim($_POST['email']);
    $nuevo_telefono = trim($_POST['telefono']); // Nuevo campo para el teléfono

    $sql_update = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nuevo_nombre, $nuevo_email, $nuevo_telefono, $usuario_id);

    if ($stmt_update->execute()) {
        $mensaje = "Datos actualizados correctamente.";
        $_SESSION['nombre'] = $nuevo_nombre;
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_contraseña'])) {
    $contraseña_actual = $_POST['contraseña_actual'];
    $nueva_contraseña = $_POST['nueva_contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    $sql_pass = "SELECT contraseña FROM usuarios WHERE id = ?";
    $stmt_pass = $conn->prepare($sql_pass);
    $stmt_pass->bind_param("i", $usuario_id);
    $stmt_pass->execute();
    $result_pass = $stmt_pass->get_result();
    $usuario_pass = $result_pass->fetch_assoc();

    if (password_verify($contraseña_actual, $usuario_pass['contraseña'])) {
        if ($nueva_contraseña === $confirmar_contraseña) {
            $hash_contraseña = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE usuarios SET contraseña = ? WHERE id = ?";
            $stmt_update_pass = $conn->prepare($sql_update_pass);
            $stmt_update_pass->bind_param("si", $hash_contraseña, $usuario_id);

            if ($stmt_update_pass->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
            } else {
                $mensaje = "Error al actualizar la contraseña.";
            }
        } else {
            $mensaje = "Las contraseñas no coinciden.";
        }
    } else {
        $mensaje = "La contraseña actual es incorrecta.";
    }
}

// Cerrar sesión
if (isset($_POST['cerrar_sesion'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="img/logo.png" alt="Logo" class="logo">
        <h4 class="text-center">⚽ Perfil de Usuario</h4>
        <a href="admin_panel.php"><i class="bi bi-house-door"></i> Regresar al Panel</a>
        <a href="perfil_usuario.php" class="active"><i class="bi bi-person"></i> Mi Perfil</a>
        <form method="POST">
            <button type="submit" name="cerrar_sesion" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </div>

    <!-- Contenido Principal -->
    <div class="content container">
        <h2 class="text-center">Perfil de Usuario</h2>

        <?php if (isset($mensaje)) { ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
        <?php } ?>

        <form method="POST" class="card p-4 mb-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
            </div>

            <button type="submit" name="actualizar_datos" class="btn btn-primary">Actualizar Datos</button>
        </form>

        <hr>

        <h3>Cambiar Contraseña</h3>
        <form method="POST" class="card p-4 mb-4">
            <div class="mb-3">
                <label for="contraseña_actual" class="form-label">Contraseña Actual</label>
                <input type="password" name="contraseña_actual" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="nueva_contraseña" class="form-label">Nueva Contraseña</label>
                <input type="password" name="nueva_contraseña" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirmar_contraseña" class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmar_contraseña" class="form-control" required>
            </div>

            <button type="submit" name="cambiar_contraseña" class="btn btn-danger">Cambiar Contraseña</button>
        </form>

        <hr>

        <!-- Botón de regreso al panel de administración -->
        <div class="text-center">
            <a href="admin_panel.php" class="btn btn-secondary">Regresar al Panel de Administración</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>