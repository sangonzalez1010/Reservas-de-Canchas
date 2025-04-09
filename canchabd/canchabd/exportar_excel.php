<?php
require('config.php');

// Obtener filtros desde el formulario
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado = $_GET['estado'] ?? '';

// Consulta SQL con filtros
$sql = "SELECT reservas.*, usuarios.nombre AS usuario_nombre, canchas.nombre AS cancha_nombre, canchas.precio 
        FROM reservas
        JOIN usuarios ON reservas.usuario_id = usuarios.id
        JOIN canchas ON reservas.cancha_id = canchas.id
        WHERE reservas.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

if (!empty($estado)) {
    $sql .= " AND reservas.estado = '$estado'";
}

$result = $conn->query($sql);

// Configurar el archivo Excel (versión antigua para compatibilidad)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Reporte_Reservas.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Crear salida en formato Excel (tabla HTML)
echo "<table border='1'>";
echo "<tr style='background-color: #007bff; color: white; font-weight: bold;'>
        <th>ID</th>
        <th>Usuario</th>
        <th>Cancha</th>
        <th>Fecha</th>
        <th>Hora Inicio</th>
        <th>Hora Fin</th>
        <th>Precio</th>
        <th>Estado</th>
      </tr>";

$total_ingresos = 0;

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['usuario_nombre']}</td>
            <td>{$row['cancha_nombre']}</td>
            <td>" . date("d/m/Y", strtotime($row['fecha'])) . "</td>
            <td>" . date("H:i", strtotime($row['hora_inicio'])) . "</td>
            <td>" . date("H:i", strtotime($row['hora_fin'])) . "</td>
            <td>$" . number_format($row['precio'], 2, ',', '.') . "</td>
            <td>" . ucfirst($row['estado']) . "</td>
          </tr>";
    $total_ingresos += $row['precio'];
}

// Agregar fila del total
echo "<tr style='background-color: #28a745; color: white; font-weight: bold;'>
        <td colspan='6'>TOTAL INGRESOS</td>
        <td colspan='2'>$" . number_format($total_ingresos, 2, ',', '.') . "</td>
      </tr>";

echo "</table>";
?>
