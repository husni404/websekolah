<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once __DIR__ . '/init.php';

/**
 * Import data siswa dari file Excel ke tabel `siswa`.
 *
 * Return:
 *  - ['ok' => true, 'count' => int]
 *  - ['ok' => false, 'duplikat' => string[], 'message' => string]
 */
function import_siswa_from_excel(string $path): array
{
    try {
        if (!class_exists(IOFactory::class)) {
            return [
                'ok' => false,
                'duplikat' => [],
                'message' => 'Library PhpSpreadsheet belum terinstal. Jalankan composer install terlebih dahulu.',
            ];
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        if ($highestRow < 2) {
            return [
                'ok' => false,
                'duplikat' => [],
                'message' => 'File Excel kosong atau tidak berisi data.',
            ];
        }

        $rows = [];
        $nisnList = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $nisn = trim((string)$sheet->getCell("A{$row}")->getValue());
            $nama = trim((string)$sheet->getCell("B{$row}")->getValue());
            $idKelas = trim((string)$sheet->getCell("C{$row}")->getValue());
            $alamat = trim((string)$sheet->getCell("D{$row}")->getValue());

            if ($nisn === '' && $nama === '') {
                continue; // baris kosong
            }

            if ($nisn === '' || $nama === '') {
                return [
                    'ok' => false,
                    'duplikat' => [],
                    'message' => "Baris {$row}: NISN dan Nama wajib diisi.",
                ];
            }

            $rows[] = [
                'nisn' => $nisn,
                'nama' => $nama,
                'id_kelas' => $idKelas,
                'alamat' => $alamat,
            ];
            $nisnList[] = $nisn;
        }

        if (!$rows) {
            return [
                'ok' => false,
                'duplikat' => [],
                'message' => 'Tidak ada baris data yang valid di file.',
            ];
        }

        // 1. Duplikat di dalam file
        $counts = array_count_values($nisnList);
        $dupeInFile = [];
        foreach ($counts as $nisn => $count) {
            if ($count > 1) {
                $dupeInFile[] = $nisn;
            }
        }

        // 2. Duplikat terhadap DB
        $pdo = pdo();
        $dupeInDb = [];
        if (count($nisnList) > 0) {
            $placeholders = implode(',', array_fill(0, count($nisnList), '?'));
            $stmt = $pdo->prepare("SELECT nisn FROM siswa WHERE nisn IN ($placeholders)");
            $stmt->execute($nisnList);
            $dupeInDb = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        }

        $allDupes = array_values(array_unique(array_merge($dupeInFile, $dupeInDb)));
        if (!empty($allDupes)) {
            sort($allDupes);
            return [
                'ok' => false,
                'duplikat' => $allDupes,
                'message' => 'Terdapat NISN duplikat di file atau database.',
            ];
        }

        // 3. Insert dengan transaksi
        $pdo->beginTransaction();
        $insert = $pdo->prepare(
            'INSERT INTO siswa (nisn, nama, id_kelas, alamat) VALUES (?, ?, ?, ?)'
        );
        foreach ($rows as $r) {
            $idKelasValue = (ctype_digit((string)$r['id_kelas'])) ? (int)$r['id_kelas'] : null;
            $alamatValue = $r['alamat'] !== '' ? $r['alamat'] : null;
            $insert->execute([$r['nisn'], $r['nama'], $idKelasValue, $alamatValue]);
        }
        $pdo->commit();

        return [
            'ok' => true,
            'count' => count($rows),
            'duplikat' => [],
            'message' => 'Import berhasil.',
        ];
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return [
            'ok' => false,
            'duplikat' => [],
            'message' => 'Terjadi error saat import: ' . $e->getMessage(),
        ];
    }
}

