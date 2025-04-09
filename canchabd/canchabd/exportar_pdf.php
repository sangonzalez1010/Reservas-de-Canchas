<?php
require('fpdf/fpdf.php');
include 'config.php';

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];
$estado = $_GET['estado'] ?? '';

// Consulta SQL con filtros
$sql = "SELECT reservas.*, usuarios.nombre AS usuario_nombre, canchas.nombre AS cancha_nombre, canchas.precio 
        FROM reservas
        JOIN usuarios ON reservas.usuario_id = usuarios.id
        JOIN canchas ON reservas.cancha_id = canchas.id
        WHERE reservas.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

if (!empty($estado)) {
    $sql .= " AND reservas.estado = '$estado'";
}

$result = $conn->query($sql);

// Crear PDF
class PDF extends FPDF {
    function Header() {
        // Logo (si tienes un logo, guárdalo en la carpeta y cámbialo aquí)
        $this->Image('img/logo.png', 10, 6, 30);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(190, 10, utf8_decode("Reporte de Reservas - CanchaBD"), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 5, "Generado el: " . date("d/m/Y H:i"), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode("Página ") . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(50, 50, 50);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(40, 10, "Usuario", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Cancha", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Fecha", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Inicio", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Fin", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Precio", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Estado", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

$total_ingresos = 0;

while ($row = $result->fetch_assoc()) {
    $total_ingresos += $row['precio'];

    $pdf->Cell(40, 10, utf8_decode($row['usuario_nombre']), 1);
    $pdf->Cell(40, 10, utf8_decode($row['cancha_nombre']), 1);
    $pdf->Cell(30, 10, date("d/m/Y", strtotime($row['fecha'])), 1);
    $pdf->Cell(20, 10, date("H:i", strtotime($row['hora_inicio'])), 1);
    $pdf->Cell(20, 10, date("H:i", strtotime($row['hora_fin'])), 1);
    $pdf->Cell(20, 10, "$" . number_format($row['precio'], 2, ',', '.'), 1, 0, 'R');
    $pdf->Cell(20, 10, ucfirst($row['estado']), 1, 1, 'C');
}

// Mostrar total de ingresos
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 10, "Total de Ingresos: ", 1, 0, 'R', true);
$pdf->Cell(40, 10, "$" . number_format($total_ingresos, 2, ',', '.'), 1, 1, 'R', true);

$pdf->Output();
?>
