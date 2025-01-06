<?php

    require("fpdf/fpdf.php");
    function generarAcuse($filePath, $boleta, $nombre, $casillero) {
        // Crear una instancia de FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Header con la imagen
        $headerImage = __DIR__.'./header.png'; // Ruta de la imagen del header
        if (file_exists($headerImage)) {
            $pdf->Image($headerImage, 10, 10, 190); // Imagen del header
            $pdf->Ln(50); // Espacio debajo del header
        }
    
        // Fecha actual
        date_default_timezone_set('America/Mexico_City');
        $fecha = "A " . date("j") . " " . date("F") . " de " . date("Y") . " en Ciudad de México.";
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];
        $fecha = str_replace(array_keys($meses), array_values($meses), $fecha);
    
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, mb_convert_encoding($fecha, "ISO-8859-1", "UTF-8"), 0, 1, 'R');
    
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, mb_convert_encoding('Acuse de Asignación de Casillero', "ISO-8859-1", "UTF-8"), 0, 1, 'C');
    
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, mb_convert_encoding('Número de Boleta:', "ISO-8859-1", "UTF-8"), 0, 0);
        $pdf->Cell(0, 10, $boleta, 0, 1, "R");
        
        $pdf->Cell(50, 10, 'Nombre Completo:', 0, 0);
        $pdf->Cell(0, 10, mb_convert_encoding($nombre, "ISO-8859-1", "UTF-8"), 0, 1, "R");
        
        $pdf->Cell(05, 10, 'Periodo del Uso del Casillero:', 0, 0);
        $pdf->Cell(0, 10, 'Semestre 2024-2025/2 (Febrero - Agosto)', 0, 1, "R");
        $pdf->Ln(20);
    
        $pdf->Cell(50, 10, mb_convert_encoding('Número de Casillero Asignado:', "ISO-8859-1", "UTF-8"), 0, 0);
        $pdf->SetFont('Arial', 'B', 12); // Resaltado en negritas
        $pdf->Cell(0, 10, $casillero, 0, 1, 'R');
        $pdf->Ln(20);
    
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Firma del Estudiante:', 0, 1, "C");
        $pdf->Ln(10);
        $pdf->Cell(0, 10, '____________________________', 0, 1, "C");
        $pdf->Cell(0, 10, mb_convert_encoding($nombre, "ISO-8859-1", "UTF-8"), 0, 1, "C");
    
        // Guardar el PDF en la ruta especificada
        $pdf->Output('F', $filePath);
    
        return "El archivo PDF se ha generado correctamente en: $filePath";
    }
?>