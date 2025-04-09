<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Obtener rol actual del usuario
    $sql = "SELECT rol_id FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Cambiar el rol (este ejemplo cambia entre admin y cliente)
    $new_role = ($user['rol_id'] == 1) ? 3 : 1;

    $sql = "UPDATE usuarios SET rol_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $new_role, $user_id);
    $stmt->execute();

    header("Location: admin_usuarios.php");
}
?>
