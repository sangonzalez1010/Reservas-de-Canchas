<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Obtener nombre del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$nombre_usuario = ($result_usuario->num_rows > 0) ? $result_usuario->fetch_assoc()['nombre'] : "Usuario";

// Obtener canchas disponibles
$sql = "SELECT id, nombre, ubicacion, precio, mantenimiento FROM canchas WHERE disponibilidad = 'disponible'";
$canchas_result = $conn->query($sql);

// Inicializar valores
$horarios_ocupados = [];
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : "";
$cancha_id = isset($_POST['cancha_id']) ? $_POST['cancha_id'] : 0;

// Obtener horarios ocupados
if ($fecha && $cancha_id) {
    $sql_horarios = "SELECT hora_inicio, hora_fin FROM reservas WHERE cancha_id = ? AND fecha = ?";
    $stmt = $conn->prepare($sql_horarios);
    $stmt->bind_param("is", $cancha_id, $fecha);
    $stmt->execute();
    $result_horarios = $stmt->get_result();

    while ($row = $result_horarios->fetch_assoc()) {
        $horarios_ocupados[] = [
            "inicio" => $row['hora_inicio'],
            "fin" => $row['hora_fin']
        ];
    }
}

// Función para verificar disponibilidad de horario
function estaOcupado($inicio, $fin, $reservas) {
    foreach ($reservas as $reserva) {
        if (
            ($inicio >= $reserva["inicio"] && $inicio < $reserva["fin"]) || 
            ($fin > $reserva["inicio"] && $fin <= $reserva["fin"]) ||
            ($inicio <= $reserva["inicio"] && $fin >= $reserva["fin"]) 
        ) {
            return true;
        }
    }
    return false;
}

// Procesar la reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservar'])) {
    $hora_inicio = $_POST['hora_inicio'];
    $duracion = $_POST['duracion'];
    $hora_fin = date('H:i', strtotime($hora_inicio) + ($duracion * 3600));
    $estado = 'pendiente';

    // Verificar si la cancha está en mantenimiento
    $sql_mantenimiento = "SELECT mantenimiento FROM canchas WHERE id = ?";
    $stmt_mantenimiento = $conn->prepare($sql_mantenimiento);
    $stmt_mantenimiento->bind_param("i", $cancha_id);
    $stmt_mantenimiento->execute();
    $result_mantenimiento = $stmt_mantenimiento->get_result();
    $cancha = $result_mantenimiento->fetch_assoc();

    if ($cancha['mantenimiento'] > 0) {
        echo "<div class='alert alert-danger' role='alert'>La cancha está en mantenimiento y no se puede reservar.</div>";
    } else {
        if (estaOcupado($hora_inicio, $hora_fin, $horarios_ocupados)) {
            echo "<div class='alert alert-danger' role='alert'>Ya hay una reserva en este horario.</div>";
        } else {
            // Insertar la reserva
            $insert_sql = "INSERT INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, estado) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iissss", $usuario_id, $cancha_id, $fecha, $hora_inicio, $hora_fin, $estado);
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success' role='alert'>Reserva realizada con éxito.</div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error al reservar: " . $stmt->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cancha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_creserva.css">
    <script>
        function actualizarHorarios() {
            var cancha_id = document.getElementById('cancha_id').value;
            var fecha = document.getElementById('fecha').value;

            if (cancha_id && fecha) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'horarios_reservados.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById('tabla_horarios').innerHTML = xhr.responseText;
                    }
                };
                xhr.send('cancha_id=' + cancha_id + '&fecha=' + fecha);
            }
        }

        function calcularPrecio() {
            var precioUnitario = parseFloat(document.getElementById('precio_unitario').value);
            var duracion = parseInt(document.getElementById('duracion').value);
            var precioTotal = precioUnitario * duracion;
            document.getElementById('precio_total').innerText = "Precio Total: $" + precioTotal.toFixed(2);
        }

        function actualizarPrecioUnitario() {
            var selectedOption = document.getElementById('cancha_id').options[document.getElementById('cancha_id').selectedIndex];
            var precioUnitario = selectedOption.getAttribute('data-precio');
            document.getElementById('precio_unitario').value = precioUnitario;
            calcularPrecio();
        }
    </script>
</head>
<body class="bg-light" style="background-image: url('img/panel.webp'); background-size: cover; background-position: center;">
    <div class="container mt-4 bg-white rounded shadow p-4">
        <h1 class="mb-3 text-center">Reservar Cancha</h1>
        <h2 class="mb-3 text-center">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?> 👋</h2>
        <div style="margin-bottom: 20px;">
            <a href="perfil.php" class="btn btn-primary">Ir a mi Perfil</a>
            <a href="login.php" class="btn btn-danger" style="margin-left: 10px;">Cerrar Sesión</a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label">Selecciona una Cancha:</label>
                        <select name="cancha_id" id="cancha_id" class="form-select" required onchange="actualizarHorarios(); actualizarPrecioUnitario()">
                            <option value="">-- Selecciona una cancha --</option>
                            <?php while ($cancha = $canchas_result->fetch_assoc()) { ?>
                                <option value="<?php echo $cancha['id']; ?>" data-precio="<?php echo $cancha['precio']; ?>">
                                    <?php echo $cancha['nombre'] . " - " . $cancha['ubicacion'] . " ($" . $cancha['precio'] . ")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" required onchange="actualizarHorarios()">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora de Inicio:</label>
                        <select name="hora_inicio" id="hora_inicio" class="form-select" required>
                            <option value="07:00">7:00 AM</option>
                            <option value="08:00">8:00 AM</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="17:00">5:00 PM</option>
                            <option value="18:00">6:00 PM</option>
                            <option value="19:00">7:00 PM</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duración (horas):</label>
                        <select name="duracion" id="duracion" class="form-select" required onchange="calcularPrecio()">
                            <option value="1">1 hora</option>
                            <option value="2">2 horas</option>
                            <option value="3">3 horas</option>
                            <option value="4">4 horas</option>
                            <option value="5">5 horas</option>
                        </select>
                    </div>
                    <input type="hidden" id="precio_unitario" value="0">
                    <div id="precio_total" class="mb-3">Precio Total: $0.00</div>
                    <button type="submit" name="reservar" class="btn btn-primary">Reservar</button>
                </form>
            </div>
            <div class="col-md-6">
                <div id="tabla_horarios" class="border p-3 rounded bg-light">
                    <h2 class="mb-3">Horarios Reservados</h2>
                    <p>Selecciona una cancha y una fecha para ver los horarios ocupados.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
