<?php
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    // Verificar que el usuario esté autenticado como administrador
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // Convertir el ID a un número entero para evitar inyecciones SQL

    // Verificar que el cliente exista
    $sql_check = "SELECT * FROM usuarios WHERE id = $id AND rol_id = (SELECT id FROM roles WHERE nombre = 'cliente')";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Primero eliminar las reservas asociadas a este cliente
        $sql_delete_reservas = "DELETE FROM reservas WHERE usuario_id = $id";
        if ($conn->query($sql_delete_reservas) === TRUE) {
            // Luego eliminar el cliente
            $sql_delete_cliente = "DELETE FROM usuarios WHERE id = $id";
            if ($conn->query($sql_delete_cliente) === TRUE) {
                // Redirigir después de la eliminación con mensaje de éxito
                header("Location: admin_clientes.php?msg=Cliente y sus reservas eliminados correctamente");
                exit();
            } else {
                echo "Error al eliminar el cliente: " . $conn->error;
            }
        } else {
            echo "Error al eliminar las reservas del cliente: " . $conn->error;
        }
    } else {
        echo "Cliente no encontrado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>
