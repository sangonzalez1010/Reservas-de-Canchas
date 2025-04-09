<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Validar el ID del cliente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de cliente no válido.");
}

$id = intval($_GET['id']);

// Obtener los datos del cliente
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']); // Nuevo campo para el teléfono

    if (empty($nombre) || empty($email) || empty($telefono)) {
        echo "<script>alert('Todos los campos son obligatorios');</script>";
    } else {
        $update_sql = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $nombre, $email, $telefono, $id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Cliente actualizado correctamente'); window.location.href='admin_clientes.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al actualizar el cliente: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_edicliente.css"> <!-- Enlace al archivo CSS -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;"> <!-- Fondo personalizado -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>✏️ Editar Cliente</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono:</label> <!-- Nuevo campo para el teléfono -->
                                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">✅ Guardar Cambios</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="admin_clientes.php" class="btn btn-secondary">⬅ Volver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
