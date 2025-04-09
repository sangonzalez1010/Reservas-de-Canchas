<?php
session_start();
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [1])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono']; // Nuevo campo para el teléfono
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar contraseña
    $rol_id = $_POST['rol_id'];
    $estado = "activo"; // Nuevo usuario comienza activo

    // Usar prepared statements para evitar inyecciones SQL
    $sql = "INSERT INTO usuarios (nombre, email, telefono, contraseña, estado, rol_id, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $nombre, $email, $telefono, $password, $estado, $rol_id);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Usuario creado con éxito.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; }
        form { background: white; padding: 20px; border-radius: 8px; width: 50%; margin: auto; box-shadow: 0px 0px 10px gray; }
        input, select { width: 90%; padding: 8px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn { padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .back { background: #6c757d; color: white; padding: 10px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>➕ Crear Usuario</h1>

    <form action="" method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="text" name="telefono" placeholder="Teléfono" required> <!-- Nuevo campo para el teléfono -->
        <input type="password" name="password" placeholder="Contraseña" required>
        
        <select name="rol_id" required>
            <option value="1">Admin</option>
            <option value="2">Empleado</option>
        </select>

        <button type="submit" class="btn">Crear Usuario</button>
    </form>

    <a href="admin_usuarios.php" class="back">⬅ Volver</a>

</body>
</html>