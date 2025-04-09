<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $cancha_id = $_GET['id'];

    // Obtener el estado actual
    $sql = "SELECT mantenimiento FROM canchas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cancha_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cancha = $result->fetch_assoc();

    if ($cancha) {
        // Alternar el estado
        $nuevo_estado = $cancha['mantenimiento'] ? 0 : 1;
        $update_sql = "UPDATE canchas SET mantenimiento = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('ii', $nuevo_estado, $cancha_id);
        $stmt->execute();
    }
}

header("Location: admin_canchas.php");
exit();
