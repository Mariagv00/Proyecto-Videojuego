<?php
require('../FPDF-master/fpdf.php');
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
  exit('Acceso no autorizado.');
}

$id_pedido = $_GET['id'];
$id_cliente = $_SESSION['id_usuario'];

$conexion = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener datos del pedido y cliente
$stmt = $conexion->prepare("
  SELECT p.*, c.nombre, c.correo 
  FROM pedidos p 
  JOIN clientes c ON p.id_cliente = c.id 
  WHERE p.id = ? AND p.id_cliente = ?
");
$stmt->execute([$id_pedido, $id_cliente]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
  exit('Factura no encontrada.');
}

// Generar PDF con FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Factura de Compra',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(10);

$pdf->Cell(0,10,mb_convert_encoding('Cliente: ' . $pedido['nombre'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Email: ' . $pedido['correo'], 'ISO-8859-1', 'UTF-8'),0,1);
$detalleSeguro = str_replace('€', chr(128), $pedido['detalle']);
$pdf->MultiCell(0, 10, 'Detalle: ' . $detalleSeguro);
$pdf->Cell(0, 10, 'Total: ' . number_format($pedido['total'], 2) . ' ' . chr(128), 0, 1);
$pdf->Cell(0,10,mb_convert_encoding('Fecha: ' . date('d/m/Y', strtotime($pedido['fecha'])), 'ISO-8859-1', 'UTF-8'),0,1);



$pdf->Output('I', 'factura_' . $pedido['id'] . '.pdf');
