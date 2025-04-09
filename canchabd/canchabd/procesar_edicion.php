<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $rol_id = intval($_POST['rol_id']);
    $estado = $conn->real_escape_string($_POST['estado']);

    $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', rol_id=$rol_id, estado='$estado' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_usuarios.php?msg=Usuario actualizado correctamente");
        exit();
    } else {
        echo "Error al actualizar usuario: " . $conn->error;
    }
} else {
    echo "Método no permitido.";
}
?>
