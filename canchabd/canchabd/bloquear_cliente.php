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

    if ($estado_actual === 'activo') { // Aseguramos que está activo antes de cambiarlo
        // Cambiar estado a inactivo
        $sql_update = "UPDATE usuarios SET estado = 'inactivo' WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "✅ Usuario bloqueado correctamente.";
            header("Location: admin_clientes.php"); // Redireccionar después de bloquear
            exit();
        } else {
            echo "❌ Error al bloquear usuario.";
        }
        $stmt->close();
    } else {
        echo "⚠️ Este usuario ya está bloqueado. Estado actual: " . htmlspecialchars($estado_actual);
    }

    $conn->close();
} else {
    echo "❌ ID de usuario no recibido.";
}
?>
