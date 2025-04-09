<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $reserva_id = $_GET['id'];

    // Preparamos la consulta para evitar inyecciones SQL
    $sql = "UPDATE reservas SET estado = 'confirmada' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reserva_id);

    // Intentamos ejecutar la consulta
    if ($stmt->execute()) {
        // Si la consulta fue exitosa, almacenamos un mensaje de éxito
        $_SESSION['message'] = "Reserva confirmada correctamente.";
    } else {
        // Si hubo un error al ejecutar la consulta, almacenamos un mensaje de error
        $_SESSION['error'] = "Hubo un error al confirmar la reserva. Inténtalo de nuevo.";
    }
} else {
    $_SESSION['error'] = "ID de reserva no válido.";
}

header("Location: admin_reservas.php");
exit();
?>
