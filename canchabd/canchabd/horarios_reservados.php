<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancha_id']) && isset($_POST['fecha'])) {
    $cancha_id = $_POST['cancha_id'];
    $fecha = $_POST['fecha'];

    $sql = "SELECT hora_inicio, hora_fin FROM reservas WHERE cancha_id = ? AND fecha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $cancha_id, $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table border='1'>";
    echo "<tr><th>Hora Inicio</th><th>Hora Fin</th></tr>";
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['hora_inicio'] . "</td><td>" . $row['hora_fin'] . "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No hay reservas para esta fecha</td></tr>";
    }
    
    echo "</table>";
}
?>
