<?php
session_start(); // Inicia la sesión para verificar los datos del usuario logueado

// Verifica si el usuario ha iniciado sesión y si tiene el rol adecuado (1 = admin, 2 = empleado)
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php"); // Si no está logueado o no tiene el rol adecuado, redirige al login
    exit();
}

include 'config.php'; // Incluye el archivo de configuración para establecer la conexión a la base de datos

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener el nombre del usuario con el ID almacenado en la sesión
$sql_usuario = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario); // Prepara la consulta SQL
$stmt_usuario->bind_param("i", $usuario_id); // Vincula el parámetro (ID del usuario)
$stmt_usuario->execute(); // Ejecuta la consulta
$result_usuario = $stmt_usuario->get_result(); // Obtiene el resultado de la consulta
$usuario = $result_usuario->fetch_assoc(); // Extrae los datos del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Estilos personalizados -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

<!-- Contenedor principal con barra lateral y contenido -->
<div class="d-flex">
    <!-- Sidebar (barra lateral de navegación) -->
    <div class="sidebar">
        <img src="img/logo.png" alt="Logo" class="logo"> <!-- Logo de la aplicación -->
        <h4 class="text-center">⚽ Admin Panel</h4>
        <!-- Enlaces de navegación -->
        <a href="admin_canchas.php"><i class="bi bi-soccer-ball"></i> Canchas</a>
        <a href="admin_reservas.php"><i class="bi bi-calendar-check"></i> Reservas</a>

        <!-- Mostrar el enlace de "Usuarios" solo si el usuario es admin (rol_id == 1) -->
        <?php if ($_SESSION['rol_id'] == 1) { ?>
            <a href="admin_usuarios.php"><i class="bi bi-people"></i> Usuarios</a>
        <?php } ?>

        <a href="admin_clientes.php"><i class="bi bi-person-badge"></i> Clientes</a>
        <a href="reportes.php"><i class="bi bi-bar-chart"></i> Reportes</a>
        <hr>
        <!-- Enlaces de navegación adicionales -->
        <a href="admin_panel.php" class="text-warning"><i class="bi bi-house-door"></i> Inicio</a>
        <a href="login.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
    </div>

    <!-- Contenido Principal -->
    <div class="content container">
        <h1 class="mb-4 text-center"><i class="bi bi-tools"></i> Panel de Administración</h1>

        <!-- Mensaje de bienvenida para el usuario -->
        <div class="alert alert-info text-center">
            Bienvenido, <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>! 
            <a href="perfil_usuario.php" class="btn btn-link">Ir a mi perfil</a> <!-- Enlace al perfil del usuario -->
        </div>

        <!-- Sección con tarjetas de acceso rápido a las diferentes funcionalidades -->
        <div class="row">
            <div class="col-md-4">
                <div class="card sport-card">
                    <img src="img/fondo-3.webp" alt="Canchas">
                    <div class="card-body text-center">
                        <h5 class="card-title">Canchas Disponibles</h5>
                        <a href="admin_canchas.php" class="btn btn-primary">Gestionar</a> <!-- Enlace para gestionar las canchas -->
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sport-card">
                    <img src="img/reserva.jpg" alt="Reservas">
                    <div class="card-body text-center">
                        <h5 class="card-title">Reservas Activas</h5>
                        <a href="admin_reservas.php" class="btn btn-success">Gestionar</a> <!-- Enlace para gestionar reservas -->
                    </div>
                </div>
            </div>

            <!-- Mostrar esta sección solo si el usuario es admin -->
            <?php if ($_SESSION['rol_id'] == 1) { ?>
            <div class="col-md-4">
                <div class="card sport-card">
                    <img src="img/aa.avif" alt="Usuarios">
                    <div class="card-body text-center">
                        <h5 class="card-title">Usuarios</h5>
                        <a href="admin_usuarios.php" class="btn btn-warning text-dark">Gestionar</a> <!-- Enlace para gestionar usuarios -->
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- Sección adicional para gestionar clientes y ver reportes -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card sport-card">
                    <img src="img/clientes.webp" alt="Clientes">
                    <div class="card-body text-center">
                        <h5 class="card-title">Clientes</h5>
                        <a href="admin_clientes.php" class="btn btn-info">Gestionar</a> <!-- Enlace para gestionar clientes -->
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sport-card">
                    <img src="img/reportes.avif" alt="Reportes">
                    <div class="card-body text-center">
                        <h5 class="card-title">Reportes</h5>
                        <a href="reportes.php" class="btn btn-dark">Ver</a> <!-- Enlace para ver reportes -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
