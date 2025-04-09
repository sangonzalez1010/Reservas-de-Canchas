<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado = $_GET['estado'] ?? '';

// Consulta de reservas con filtros
$sql = "SELECT reservas.*, usuarios.nombre AS usuario_nombre, canchas.nombre AS cancha_nombre, canchas.precio 
        FROM reservas
        JOIN usuarios ON reservas.usuario_id = usuarios.id
        JOIN canchas ON reservas.cancha_id = canchas.id
        WHERE 1";

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND reservas.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}
if (!empty($estado)) {
    $sql .= " AND reservas.estado = '$estado'";
}

$result = $conn->query($sql);

// Calcular ingresos
$total_ingresos = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['estado'] == 'confirmada') {
        $total_ingresos += $row['precio'];
    }
}
$result->data_seek(0); // Reiniciar el puntero de resultados

// Obtener los clientes con más reservas
$clientes_top_sql = "SELECT usuarios.nombre, COUNT(reservas.id) AS total_reservas 
                     FROM reservas 
                     JOIN usuarios ON reservas.usuario_id = usuarios.id
                     WHERE reservas.estado = 'confirmada'
                     GROUP BY reservas.usuario_id 
                     ORDER BY total_reservas DESC 
                     LIMIT 3";
$clientes_top = $conn->query($clientes_top_sql);

// Obtener las canchas más reservadas
$canchas_top_sql = "SELECT canchas.nombre, COUNT(reservas.id) AS total_reservas 
                    FROM reservas 
                    JOIN canchas ON reservas.cancha_id = canchas.id
                    WHERE reservas.estado = 'confirmada'
                    GROUP BY reservas.cancha_id 
                    ORDER BY total_reservas DESC 
                    LIMIT 3";
$canchas_top = $conn->query($canchas_top_sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_reportes.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

    <div class="container mt-5">
        <h1 class="text-center mb-4 text-white">📊 Reportes de Reservas</h1>

        <!-- Formulario de Filtros -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Fecha Fin:</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" <?= ($estado == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="confirmada" <?= ($estado == 'confirmada') ? 'selected' : '' ?>>Confirmada</option>
                        <option value="cancelada" <?= ($estado == 'cancelada') ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">🔍 Generar Reporte</button>
        </form>

        <!-- Total Ingresos -->
        <h3 class="text-white">Total Ingresos: $<?= number_format($total_ingresos, 2, ',', '.') ?></h3>

        <!-- Mejores Clientes -->
        <h2 class="text-white mt-4">🏆 Mejores Clientes</h2>
        <div class="card mb-4" style="background-color: rgba(255, 255, 255, 0.8);">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Cliente</th>
                            <th>Total Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cliente = $clientes_top->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                            <td class="highlight"><?= $cliente['total_reservas'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Canchas Más Populares -->
        <h2 class="text-white">🔥 Canchas Más Populares</h2>
        <div class="card mb-4" style="background-color: rgba(255, 255, 255, 0.8);">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Cancha</th>
                            <th>Total Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cancha = $canchas_top->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($cancha['nombre']) ?></td>
                            <td class="highlight"><?= $cancha['total_reservas'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detalle de Reservas -->
        <h2 class="text-white">📅 Detalle de Reservas</h2>
        <div class="card mb-4" style="background-color: rgba(255, 255, 255, 0.8);">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Cancha</th>
                            <th>Fecha</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Precio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reserva = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['id']) ?></td>
                            <td><?= htmlspecialchars($reserva['usuario_nombre']) ?></td>
                            <td><?= htmlspecialchars($reserva['cancha_nombre']) ?></td>
                            <td><?= date("d/m/Y", strtotime($reserva['fecha'])) ?></td>
                            <td><?= date("H:i", strtotime($reserva['hora_inicio'])) ?></td>
                            <td><?= date("H:i", strtotime($reserva['hora_fin'])) ?></td>
                            <td>$<?= number_format($reserva['precio'], 2, ',', '.') ?></td>
                            <td><?= ucfirst($reserva['estado']) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botones para Exportar -->
        <div class="text-center">
            <a href="exportar_pdf.php?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&estado=<?= $estado ?>" class="btn btn-danger">📄 Exportar PDF</a>
            <a href="exportar_excel.php?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&estado=<?= $estado ?>" class="btn btn-success">📊 Exportar Excel</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
