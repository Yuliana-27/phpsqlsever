<?php
require '../conexion.php'; // Conexión a la base de datos
require './fpdf.php';

class CustomPDF extends FPDF
{

    // Variables para las fechas del rango de consulta
    public $fecha_inicio;
    public $fecha_fin;

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
        $this->Cell(100, 10, utf8_decode("REPORTE DE ASISTENCIA POR PROVEEDOR"), 0, 1, 'C', 0);
        $this->Ln(7);

         // Mostrar rango de fechas consultado
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Rango de consulta: ' . $this->fecha_inicio . ' al ' . $this->fecha_fin, 0, 1, 'C');
        $this->Ln(5);

        $this->SetFillColor(125, 173, 221);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(163, 163, 163);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'ID', 1, 0, 'C', 1);
        $this->Cell(80, 10, 'Proveedor', 1, 0, 'C', 1);
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

// Obtener datos del formulario
$proveedor = $_POST['proveedor'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

// Validar entrada
if (empty($proveedor) || empty($fecha_inicio) || empty($fecha_fin)) {
    die('Error: Todos los campos son obligatorios.');
}

// Consultar los datos
$sql = "SELECT id, proveedor, fecha_entrada, fecha_salida 
        FROM asistencia_proveedor 
        WHERE proveedor = ? 
        AND fecha_entrada BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$proveedor, $fecha_inicio, $fecha_fin]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new CustomPDF();
$pdf->fecha_inicio = date('d/m/Y', strtotime($fecha_inicio)); // Convertir formato de fecha
$pdf->fecha_fin = date('d/m/Y', strtotime($fecha_fin));       // Convertir formato de fecha
$pdf->AddPage("landscape");
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 10);
$pdf->SetDrawColor(163, 163, 163);

if (empty($resultados)) {
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, 'No hay registros en este rango de fechas.', 0, 1, 'C');
} else {
    foreach ($resultados as $row) {
        $pdf->Cell(30, 10, $row['id'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['proveedor'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['fecha_entrada'], 1, 0, 'C', 0);
        $pdf->Cell(80, 10, $row['fecha_salida'], 1, 1, 'C', 0);
    }
}

$pdf->Output('Reporte_Asistencia_XProveedor.pdf', 'I');
// Salida del PDF
$pdf->Output();
?>
