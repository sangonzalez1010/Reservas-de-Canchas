<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {

    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';

// Verifica si el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    // Obtiene el ID de la cancha a eliminar
    $cancha_id = $_GET['id'];

    // Prepara la consulta para verificar si la cancha existe
    $check_sql = "SELECT * FROM canchas WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $cancha_id); // Vincula el parámetro
    $stmt->execute(); // Ejecuta la consulta
    $result = $stmt->get_result(); // Obtiene el resultado

    // Si la cancha existe, procede a eliminarla
    if ($result->num_rows > 0) {
        // Prepara la consulta para eliminar la cancha
        $delete_sql = "DELETE FROM canchas WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $cancha_id); // Vincula el parámetro
        if ($stmt->execute()) {
            // Si se elimina correctamente, guarda un mensaje en la sesión
            $_SESSION['mensaje'] = "Cancha eliminada correctamente.";
        } else {
            // Si ocurre un error, guarda el mensaje de error en la sesión
            $_SESSION['error'] = "Error al eliminar la cancha.";
        }
    } else {
        // Si la cancha no existe, guarda el mensaje de error en la sesión
        $_SESSION['error'] = "La cancha no existe.";
    }
}

// Redirige a la página de administración de canchas después de la eliminación
header("Location: admin_canchas.php");
exit(); // Asegura que no se ejecute más código después de la redirección
?>
