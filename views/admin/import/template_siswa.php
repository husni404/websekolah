<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once __DIR__ . '/../../../app/init.php';

if (!class_exists(Spreadsheet::class)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Library PhpSpreadsheet belum terinstal.\n";
    echo "Jalankan: composer install";
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Siswa');

// Header
$sheet->setCellValue('A1', 'nisn');
$sheet->setCellValue('B1', 'nama');
$sheet->setCellValue('C1', 'id_kelas');
$sheet->setCellValue('D1', 'alamat');

// Contoh baris
$sheet->setCellValue('A2', '0099123456');
$sheet->setCellValue('B2', 'Nama Contoh');
$sheet->setCellValue('C2', '1');
$sheet->setCellValue('D2', 'Jl. Contoh No. 1');

// Style sederhana
$sheet->getStyle('A1:D1')->getFont()->setBold(true);

$filename = 'template_siswa_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

