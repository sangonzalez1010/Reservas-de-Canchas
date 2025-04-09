<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM usuarios WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Usuario no encontrado.");
}

$user = $result->fetch_assoc();

// Obtener roles para el select
$roles_result = $conn->query("SELECT * FROM roles");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_editusuario.css"> <!-- Archivo CSS separado -->
</head>
<body style="background: url('img/fondo-3.webp') no-repeat center center fixed; background-size: cover; color: #333;"> <!-- Imagen de fondo en línea -->

    <div class="container mt-5">
        <h1>✏️ Editar Usuario</h1>
        <div class="card">
            <div class="card-body">
                <form action="procesar_edicion.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="rol_id" class="form-label">Rol:</label>
                        <select name="rol_id" class="form-select" required>
                            <?php while ($rol = $roles_result->fetch_assoc()) { ?>
                                <option value="<?php echo $rol['id']; ?>" <?php echo ($rol['id'] == $user['rol_id']) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($rol['nombre']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select name="estado" class="form-select" required>
                            <option value="activo" <?php echo ($user['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="bloqueado" <?php echo ($user['estado'] == 'bloqueado') ? 'selected' : ''; ?>>Bloqueado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-custom w-100">✅ Guardar Cambios</button>
                </form>
                <a href="admin_usuarios.php" class="btn-cancel w-100 text-center">❌ Cancelar</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
