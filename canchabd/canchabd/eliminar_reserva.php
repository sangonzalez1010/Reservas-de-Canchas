<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {

    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verificar si la reserva existe
    $check_sql = "SELECT * FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eliminar la reserva
        $delete_sql = "DELETE FROM reservas WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Reserva eliminada correctamente.";
        } else {
            $_SESSION['error'] = "Error al eliminar la reserva.";
        }
    } else {
        $_SESSION['error'] = "La reserva no existe.";
    }
}

header("Location: admin_reservas.php");
exit();