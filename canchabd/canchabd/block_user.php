<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Obtener estado actual del usuario
    $sql = "SELECT estado FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['estado'] == 'activo') {
        // Bloquear usuario
        $sql = "UPDATE usuarios SET estado = 'bloqueado' WHERE id = ?";
    } else {
        // Desbloquear usuario
        $sql = "UPDATE usuarios SET estado = 'activo' WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    header("Location: admin_usuarios.php");
}
?>
