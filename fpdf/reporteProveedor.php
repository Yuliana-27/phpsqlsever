<?php

require('./fpdf.php');

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        include '../conexion.php'; // Conexión con la BD
        
        // Consulta de información de la empresa
        $consulta_info = $conn->query("SELECT nombre, telefono, ubicacion, ruc FROM empresa");
        $dato_info = $consulta_info->fetch(PDO::FETCH_ASSOC);

        // Verificar si los datos de la empresa están disponibles
        if (!$dato_info) {
            $dato_info = [
                'nombre' => 'Nombre no disponible',
                'telefono' => 'Teléfono no disponible',
                'ubicacion' => 'Ubicación no disponible',
                'ruc' => 'RUC no disponible'
            ];
        }

        // Logo de la empresa
        $this->Image('../img/logo.jpg', 10, 5, 20); // Logo (ajusta la ruta y el tamaño)
        $this->SetFont('Arial', 'B', 19);
        $this->Cell(95); // Movernos a la derecha
        $this->SetTextColor(0, 0, 0);
        
        // Título del encabezado con el nombre de la empresa
        $this->Cell(110, 15, utf8_decode($dato_info['nombre']), 0, 1, 'C', 0);
        $this->Ln(3);
        
        // Detalles de la empresa: Ubicación, Teléfono y RUC
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

        // Título del reporte
        $this->SetTextColor(0, 95, 189);
        $this->Cell(90);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(100, 10, utf8_decode("REPORTE DE PROVEEDORES"), 0, 1, 'C', 0);
        $this->Ln(7);

        // Encabezado de la tabla
        $this->SetFillColor(125, 173, 221);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(163, 163, 163);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', 1);
        $this->Cell(60, 10, utf8_decode('NOMBRE COMPLETO'), 1, 0, 'C', 1);
        $this->Cell(50, 10, utf8_decode('PROVEEDOR'), 1, 0, 'C', 1);
        $this->Cell(40, 10, utf8_decode('PLACAS '), 1, 0, 'C', 1);
        $this->Cell(50, 10, utf8_decode('MODELO Y MARCA'), 1, 0, 'C', 1);
        $this->Cell(30, 10, utf8_decode('COLOR '), 1, 0, 'C', 1);
        $this->Cell(30, 10, utf8_decode('ESTADO'), 1, 1, 'C', 1);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Fecha: ') . date('d/m/Y'), 0, 0, 'R');
    }
}

include '../conexion.php'; // Incluye la conexión a la BD
$pdf = new PDF();
$pdf->AddPage("landscape");
$pdf->AliasNbPages();

$i = 0;
$pdf->SetFont('Arial', '', 10);
$pdf->SetDrawColor(163, 163, 163); 

// Consulta del proveedor
$consulta_reporte_proveedores = $conn->query("SELECT 
    nombre_apellido,
    proveedor,
    placas_vehiculos,
    modelo_marca,
    color_vehiculo,
    estado
FROM proveedores");

while ($proveedores = $consulta_reporte_proveedores->fetch(PDO::FETCH_ASSOC)) {
    $i++;
    $pdf->Cell(10, 10, utf8_decode($i), 1, 0, 'C', 0);
    $pdf->Cell(60, 10, utf8_decode($proveedores['nombre_apellido']), 1, 0, 'C', 0);
    $pdf->Cell(50, 10, utf8_decode($proveedores['proveedor']), 1, 0, 'C', 0);
    $pdf->Cell(40, 10, utf8_decode($proveedores['placas_vehiculos']), 1, 0, 'C', 0);
    $pdf->Cell(50, 10, utf8_decode($proveedores['modelo_marca']), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, utf8_decode($proveedores['color_vehiculo']), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, utf8_decode($proveedores['estado']), 1, 1, 'C', 0);
}

$pdf->Output('Reporte_Proveedores.pdf', 'I'); // I para mostrar en navegador, D para descargar
?>
