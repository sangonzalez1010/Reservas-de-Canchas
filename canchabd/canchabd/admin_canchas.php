<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1, 2])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener las canchas
$sql = "SELECT * FROM canchas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Canchas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_cancha.css"> <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover;">

    <div class="container mt-5">
        <h1 class="text-center mb-4 text-white">⚽ Gestionar Canchas</h1>
        
        <div class="d-flex justify-content-between mb-3">
            <a href="admin_panel.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver al Panel</a>
            <a href="add_cancha.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Agregar Nueva Cancha</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered text-center" style="background-color: rgba(255, 255, 255, 0.8); border-radius: 10px;">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cancha = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $cancha['id']; ?></td>
                        <td><?php echo $cancha['nombre']; ?></td>
                        <td><?php echo $cancha['ubicacion']; ?></td>
                        <td><?php echo "$" . number_format($cancha['precio'], 2); ?></td>
                        <td>
                            <?php echo $cancha['mantenimiento'] ? '<span class="text-muted">En Mantenimiento</span>' : '<span class="text-success">Disponible</span>'; ?>
                        </td>
                        <td>
                            <a href="edit_cancha.php?id=<?php echo $cancha['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <a href="delete_cancha.php?id=<?php echo $cancha['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta cancha?')">
                                <i class="bi bi-trash"></i> Eliminar
                            </a>
                            <a href="mantenimiento_cancha.php?id=<?php echo $cancha['id']; ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-tools"></i> <?php echo $cancha['mantenimiento'] ? 'Quitar Mantenimiento' : 'Poner en Mantenimiento'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
