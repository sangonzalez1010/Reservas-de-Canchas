<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $precio = $_POST['precio']; // Nuevo campo para el precio

    // Insertar la nueva cancha en la base de datos
    $sql = "INSERT INTO canchas (nombre, ubicacion, precio) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssd', $nombre, $ubicacion, $precio);
    $stmt->execute();

    header("Location: admin_canchas.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cancha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_add.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;"> <!-- Fondo personalizado -->

    <div class="container mt-5">
        <h1 class="text-center text-white mb-4">Agregar Nueva Cancha</h1> <!-- Título de la página -->
        
        <div class="card shadow-lg p-4" style="background-color: rgba(255, 255, 255, 0.8);">
            <!-- Formulario de agregar cancha -->
            <form action="add_cancha.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej. Cancha Central">
                </div>
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación:</label>
                    <input type="text" name="ubicacion" class="form-control" required placeholder="Ej. Calle 123, Ciudad">
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio por Hora:</label>
                    <input type="number" name="precio" step="0.01" class="form-control" required placeholder="Ej. 50.00">
                </div>
                <button type="submit" class="btn btn-success w-100">Agregar Cancha</button> <!-- Botón para enviar el formulario -->
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
