<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {

    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verificar que el usuario exista
    $sql_check = "SELECT * FROM usuarios WHERE id = $id";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Eliminar usuario
        $sql_delete = "DELETE FROM usuarios WHERE id = $id";
        if ($conn->query($sql_delete) === TRUE) {
            header("Location: admin_usuarios.php?msg=Usuario eliminado correctamente");
            exit();
        } else {
            echo "Error al eliminar usuario: " . $conn->error;
        }
    } else {
        echo "Usuario no encontrado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>
