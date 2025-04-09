<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}


include 'config.php';

if (isset($_GET['id'])) {
    $reserva_id = $_GET['id'];

    $sql = "UPDATE reservas SET estado = 'cancelada' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reserva_id);
    $stmt->execute();
}

header("Location: admin_reservas.php");
?>
