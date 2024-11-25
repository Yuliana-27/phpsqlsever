<?php
require '../conexion.php'; // Conexión a la base de datos
require('./fpdf.php');

class CustomPDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        global $conn;

        // Consulta de información de la empresa
        $consulta_info = $conn->query("SELECT nombre, telefono, ubicacion, ruc FROM empresa");
        $dato_info = $consulta_info->fetch(PDO::FETCH_ASSOC);

        if (!$dato_info) {
            $dato_info = [
                'nombre' => 'Nombre no disponible',
                'telefono' => 'Teléfono no disponible',
                'ubicacion' => 'Ubicación no disponible',
                'ruc' => 'RUC no disponible'
            ];
        }

        $this->Image('../img/logo.jpg', 10, 5, 20); // Logo
        $this->SetFont('Arial', 'B', 19);
        $this->Cell(95);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(110, 15, utf8_decode($dato_info['nombre']), 0, 1, 'C', 0);
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(85);
        $this->Cell(96, 10, utf8_decode("Ubicación : " . $dato_info['ubicacion']), 0, 0, '', 0);
        $this->Ln(5);
        $this->Cell(85);
        $this->Cell(59, 10, utf8_decode("Teléfono : " . $dato_info['telefono']), 0, 0, '', 0);
        $this->Ln(5);
        $this->Cell(85);
        $this->Cell(85, 10, utf8_decode("RUC : " . $dato_info['ruc']), 0, 0, '', 0);
        $this->Ln(10);

        $this->SetTextColor(0, 95, 189);
        $this->Cell(90);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(100, 10, utf8_decode("REPORTE DE ASISTENCIA POR FECHA"), 0, 1, 'C', 0);
        $this->Ln(7);

        $this->SetFillColor(125, 173, 221);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(163, 163, 163);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'ID', 1, 0, 'C', 1);
        $this->Cell(80, 10, 'Nombre y Apellido', 1, 0, 'C', 1);
        $this->Cell(80, 10, 'Fecha Entrada', 1, 0, 'C', 1);
        $this->Cell(80, 10, 'Fecha Salida', 1, 1, 'C', 1);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, utf8_decode('Fecha: ') . date('d/m/Y'), 0, 0, 'R');
    }
}

// Verificar si se han enviado las fechas
if (isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    if ($fechaInicio > $fechaFin) {
        die('La fecha de inicio no puede ser mayor que la fecha de fin.');
    }

    $sql = "SELECT id, nombre_apellido, fecha_entrada, fecha_salida 
            FROM asistencia_invitado 
            WHERE fecha_entrada BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fechaInicio, $fechaFin]);
    $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($asistencias)) {
        die('No se encontraron registros en el rango de fechas seleccionado.');
    }

    $pdf = new CustomPDF();
    $pdf->AddPage("landscape");
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetDrawColor(163, 163, 163);

    foreach ($asistencias as $row) {
        $pdf->Cell(30, 10, $row['id'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['nombre_apellido'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['fecha_entrada'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['fecha_salida'], 1, 1, 'C', 0);
    }

    $pdf->Output('Reporte_Asistencia_Invitados.pdf', 'I');
} else {
    die('Por favor, seleccione un rango de fechas.');
}
