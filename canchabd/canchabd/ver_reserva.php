<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Validar que el ID de la reserva es un número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID de reserva no válido.'); window.location='admin_reservas.php';</script>";
    exit();
}

$reserva_id = intval($_GET['id']); // Convertir a entero para mayor seguridad

// Obtener los detalles de la reserva incluyendo el teléfono
$sql = "SELECT reservas.*, usuarios.nombre AS usuario_nombre, usuarios.email AS usuario_email, usuarios.telefono,
        canchas.nombre AS cancha_nombre, canchas.ubicacion AS cancha_ubicacion, canchas.precio 
        FROM reservas
        JOIN usuarios ON reservas.usuario_id = usuarios.id
        JOIN canchas ON reservas.cancha_id = canchas.id
        WHERE reservas.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reserva_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se obtuvo el resultado
if ($result->num_rows > 0) {
    $reserva = $result->fetch_assoc();
} else {
    echo "<script>alert('Reserva no encontrada.'); window.location='admin_reservas.php';</script>";
    exit();
}

// Función para asignar colores al estado
function colorEstado($estado) {
    $estados = [
        'pendiente' => ['color' => 'orange', 'texto' => 'Pendiente'],
        'aprobado' => ['color' => 'green', 'texto' => 'Aprobado'],
        'cancelado' => ['color' => 'red', 'texto' => 'Cancelado']
    ];
    $estado_lower = strtolower(trim($estado));
    return isset($estados[$estado_lower]) 
        ? "<span style='color: {$estados[$estado_lower]['color']}; font-weight: bold;'>{$estados[$estado_lower]['texto']}</span>" 
        : "<span style='color: black; font-weight: bold;'>$estado</span>";
}

// Formatear fecha y precio
$fecha_formateada = date("d/m/Y", strtotime($reserva['fecha']));
$precio_formateado = number_format($reserva['precio'], 2, ',', '.'); // Ejemplo: $50.000,00
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_verreserva.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
    <div class="details">
        <h1>Detalles de la Reserva</h1>
        <table class="table table-bordered">
            <tr><th>ID Reserva</th><td><?= htmlspecialchars($reserva['id']) ?></td></tr>
            <tr><th>Usuario</th><td><?= htmlspecialchars($reserva['usuario_nombre']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($reserva['usuario_email']) ?></td></tr>
            <tr><th>Teléfono</th><td><?= htmlspecialchars($reserva['telefono']) ?></td></tr>
            <tr><th>Cancha</th><td><?= htmlspecialchars($reserva['cancha_nombre']) ?></td></tr>
            <tr><th>Ubicación</th><td><?= htmlspecialchars($reserva['cancha_ubicacion']) ?></td></tr>
            <tr><th>Fecha</th><td><?= $fecha_formateada ?></td></tr>
            <tr><th>Precio</th><td>$<?= $precio_formateado ?></td></tr>
            <tr><th>Estado</th><td class="estado"><?= colorEstado($reserva['estado']) ?></td></tr>
        </table>
        <a href="admin_reservas.php" class="back">Volver a Reservas</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>