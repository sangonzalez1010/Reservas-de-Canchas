<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener reservas con horarios y precio de la cancha
$sql = "SELECT reservas.*, usuarios.nombre AS usuario_nombre, canchas.nombre AS cancha_nombre, canchas.precio
        FROM reservas
        JOIN usuarios ON reservas.usuario_id = usuarios.id
        JOIN canchas ON reservas.cancha_id = canchas.id";
$result = $conn->query($sql);

// Función para colorear el estado
function colorEstado($estado) {
    $colores = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada'
    ];
    return $colores[strtolower($estado)] ?? 'Desconocido';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_reservas.css"> <!-- Asegúrate de tener este archivo para estilos personalizados -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

<div class="container mt-5">
    <h1 class="text-center mb-4 text-white">📅 Gestionar Reservas</h1>
    
    <div class="card shadow-lg">
        <div class="card-body">
            <table class="table table-striped table-bordered" style="background-color: rgba(255, 255, 255, 0.8); border-radius: 10px;">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Cancha</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($reserva = $result->fetch_assoc()) { 
                    // Calcular la duración en horas
                    $hora_inicio = strtotime($reserva['hora_inicio']);
                    $hora_fin = strtotime($reserva['hora_fin']);
                    $duracion = ($hora_fin - $hora_inicio) / 3600; // Duración en horas
                    $precio_total = $reserva['precio'] * $duracion; // Calcular el precio total
                ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['id']) ?></td>
                        <td><?= htmlspecialchars($reserva['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars($reserva['cancha_nombre']) ?></td>
                        <td><?= date("d/m/Y", strtotime($reserva['fecha'])) ?></td>
                        <td><?= date("H:i", strtotime($reserva['hora_inicio'])) ?></td>
                        <td><?= date("H:i", strtotime($reserva['hora_fin'])) ?></td>
                        <td>$<?= number_format($precio_total, 2, ',', '.') ?></td> <!-- Mostrar precio total -->
                        <td><?= colorEstado($reserva['estado']) ?></td>
                        <td>
                            <?php if ($reserva['estado'] == 'pendiente') { ?>
                                <a href="confirm_reserva.php?id=<?= $reserva['id'] ?>" class="btn btn-success btn-sm">✔ Confirmar</a>
                                <a href="cancel_reserva.php?id=<?= $reserva['id'] ?>" class="btn btn-danger btn-sm">❌ Cancelar</a>
                            <?php } elseif ($reserva['estado'] == 'confirmada') { ?>
                                <span class="text-success">✅ Confirmada</span>
                            <?php } elseif ($reserva['estado'] == 'cancelada') { ?>
                                <span class="text-danger">❌ Cancelada</span>
                            <?php } ?>
                            <a href="ver_reserva.php?id=<?= $reserva['id'] ?>" class="btn btn-info btn-sm">🔍 Ver Detalles</a>
                            <a href="eliminar_reserva.php?id=<?= $reserva['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reserva?')">🗑 Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_panel.php" class="btn btn-primary">⬅ Regresar</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
