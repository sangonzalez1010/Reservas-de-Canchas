<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verificar el estado actual del usuario
    $sql_check = "SELECT estado FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($estado_actual);
    $stmt->fetch();
    $stmt->close();

    if ($estado_actual === 'inactivo') { // Aseguramos que está bloqueado antes de cambiarlo
        // Cambiar estado a activo
        $sql_update = "UPDATE usuarios SET estado = 'activo' WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "✅ Usuario desbloqueado correctamente.";
            header("Location: admin_clientes.php"); // Redireccionar después de desbloquear
            exit();
        } else {
            echo "❌ Error al desbloquear usuario.";
        }
        $stmt->close();
    } else {
        echo "⚠️ Este usuario no está bloqueado. Estado actual: " . htmlspecialchars($estado_actual);
    }

    $conn->close();
} else {
    echo "❌ ID de usuario no recibido.";
}
?>
