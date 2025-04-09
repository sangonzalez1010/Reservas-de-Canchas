<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: login.php");
    exit();
}

include 'config.php';

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']); // Nuevo campo para el teléfono

    if (!empty($nombre) && !empty($email) && !empty($telefono)) {
        $sql_update = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $nombre, $email, $telefono, $usuario_id);

        if ($stmt_update->execute()) {
            $mensaje = "<p class='text-success'>Perfil actualizado con éxito.</p>";
            $_SESSION['nombre'] = $nombre; // Actualizar el nombre en la sesión
        } else {
            $mensaje = "<p class='text-danger'>Error al actualizar el perfil.</p>";
        }
    } else {
        $mensaje = "<p class='text-danger'>Todos los campos son obligatorios.</p>";
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_contraseña'])) {
    $contraseña_actual = $_POST['contraseña_actual'];
    $nueva_contraseña = $_POST['nueva_contraseña'];

    $sql_contraseña = "SELECT contraseña FROM usuarios WHERE id = ?";
    $stmt_contraseña = $conn->prepare($sql_contraseña);
    $stmt_contraseña->bind_param("i", $usuario_id);
    $stmt_contraseña->execute();
    $result_contraseña = $stmt_contraseña->get_result();
    $usuario_contraseña = $result_contraseña->fetch_assoc();

    if (password_verify($contraseña_actual, $usuario_contraseña['contraseña'])) {
        $hash_nueva_contraseña = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
        $sql_update_contraseña = "UPDATE usuarios SET contraseña = ? WHERE id = ?";
        $stmt_update_contraseña = $conn->prepare($sql_update_contraseña);
        $stmt_update_contraseña->bind_param("si", $hash_nueva_contraseña, $usuario_id);

        if ($stmt_update_contraseña->execute()) {
            $mensaje = "<p class='text-success'>Contraseña cambiada con éxito.</p>";
        } else {
            $mensaje = "<p class='text-danger'>Error al cambiar la contraseña.</p>";
        }
    } else {
        $mensaje = "<p class='text-danger'>Contraseña actual incorrecta.</p>";
    }
}

// Obtener reservas del usuario
$sql_reservas = "SELECT r.id, c.nombre AS cancha, r.fecha, r.hora_inicio, r.hora_fin, r.estado 
                 FROM reservas r
                 JOIN canchas c ON r.cancha_id = c.id
                 WHERE r.usuario_id = ?
                 ORDER BY r.fecha DESC";
$stmt_reservas = $conn->prepare($sql_reservas);
$stmt_reservas->bind_param("i", $usuario_id);
$stmt_reservas->execute();
$reservas_result = $stmt_reservas->get_result();

// Procesar cancelación de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];

    $sql_cancelar = "UPDATE reservas SET estado = 'cancelada' WHERE id = ? AND usuario_id = ? AND estado = 'pendiente'";
    $stmt_cancelar = $conn->prepare($sql_cancelar);
    $stmt_cancelar->bind_param("ii", $reserva_id, $usuario_id);

    if ($stmt_cancelar->execute()) {
        $mensaje = "<p class='text-success'>Reserva cancelada con éxito.</p>";
        header("Refresh:0");
    } else {
        $mensaje = "<p class='text-danger'>Error al cancelar la reserva.</p>";
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
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_perfil.css"> <!-- Enlace al archivo CSS -->
    <style>
        body {
            background-image: url('img/panel.webp'); /* Cambia la ruta a tu imagen de fondo */
            background-size: cover;
            background-position: center;
            color: #333;
        }
        .card {
            border-radius: 10px; /* Bordes redondeados para la tarjeta */
        }
        .text-danger {
            color: #dc3545; /* Color para mensajes de error */
        }
        .text-success {
            color: #28a745; /* Color para mensajes de éxito */
        }
    </style>
</head>
<body class="container mt-4">
    <h1 class="mb-3 text-center">Mi Perfil</h1>
    <?php echo $mensaje; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Actualizar Perfil</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono:</label>
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                </div>
                <button type="submit" name="actualizar_perfil" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Cambiar Contraseña</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Contraseña Actual:</label>
                    <input type="password" name="contraseña_actual" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña:</label>
                    <input type="password" name="nueva_contraseña" class="form-control" required>
                </div>
                <button type="submit" name="cambiar_contraseña" class="btn btn-warning">Cambiar Contraseña</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Mis Reservas</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cancha</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reserva = $reservas_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['cancha']); ?></td>
                            <td><?php echo $reserva['fecha']; ?></td>
                            <td><?php echo $reserva['hora_inicio']; ?></td>
                            <td><?php echo $reserva['hora_fin']; ?></td>
                            <td><?php echo ucfirst($reserva['estado']); ?></td>
                            <td>
                                <?php if ($reserva['estado'] == 'pendiente') { ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="reserva_id" value="<?php echo $reserva['id']; ?>">
                                        <button type="submit" name="cancelar_reserva" class="btn btn-danger btn-sm">Cancelar</button>
                                    </form>
                                <?php } else { echo "-"; } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <form method="POST" class="mt-4">
        <button type="submit" name="cerrar_sesion" class="btn btn-secondary">Cerrar Sesión</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>