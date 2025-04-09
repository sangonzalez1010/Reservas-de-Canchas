<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (isset($_GET['id'])) {
    $cancha_id = $_GET['id'];
    $sql = "SELECT * FROM canchas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cancha_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cancha = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $precio = $_POST['precio'];

    $sql = "UPDATE canchas SET nombre = ?, ubicacion = ?, precio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssdi', $nombre, $ubicacion, $precio, $cancha_id);
    $stmt->execute();

    header("Location: admin_canchas.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cancha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_edicancha.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;"> <!-- Fondo personalizado -->

    <div class="container mt-5">
        <div class="card shadow-lg p-4" style="background-color: rgba(255, 255, 255, 0.8);">
            <h2 class="text-center text-primary">⚽ Editar Cancha</h2>
            
            <form action="edit_cancha.php?id=<?php echo $cancha['id']; ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($cancha['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación:</label>
                    <input type="text" class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($cancha['ubicacion']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio por Hora:</label>
                    <input type="number" class="form-control" name="precio" step="0.01" value="<?php echo htmlspecialchars($cancha['precio']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
