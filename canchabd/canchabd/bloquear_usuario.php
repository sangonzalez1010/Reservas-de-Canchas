<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Verificar que el usuario a bloquear es admin o empleado
    $check_sql = "SELECT * FROM usuarios WHERE id = $id AND rol_id IN (SELECT id FROM roles WHERE nombre IN ('admin', 'empleado'))";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Bloquear usuario
        $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            header("Location: admin_usuarios.php");
            exit();
        } else {
            echo "Error al bloquear el usuario: " . $conn->error;
        }
    } else {
        echo "Usuario no válido.";
    }
} else {
    echo "ID de usuario no proporcionado.";
}
?>
