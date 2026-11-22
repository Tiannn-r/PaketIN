<?php

namespace App\Services;

use RuntimeException;

class CsvimportService
{
    public function bacaCsv(string $path, array $kolomWajib): array
    {
        if (!is_file($path)) {
            throw new RuntimeException("File CSV tidak ditemukan: {$path}");
        }

        $file = fopen($path, 'rb');
        if ($file === false) {
            throw new RuntimeException("File CSV tidak dapat dibuka: {$path}");
        }

        try {
            $header = fgetcsv($file, null, ',', '"', '');
            if ($header === false || $header === [null]) {
                throw new RuntimeException("File CSV kosong atau tidak memiliki header: {$path}");
            }

            if (isset($header[0])) {
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
            }

            $header = array_map(static fn ($kolom) => trim((string) $kolom), $header);

            if (in_array('', $header, true)) {
                throw new RuntimeException("Ada nama kolom kosong pada header CSV: {$path}");
            }

            if (count($header) !== count(array_unique($header))) {
                throw new RuntimeException("Ada nama kolom ganda pada header CSV: {$path}");
            }

            $kolomTidakAda = array_diff($kolomWajib, $header);
            if ($kolomTidakAda !== []) {
                throw new RuntimeException('Kolom CSV tidak lengkap. Kolom yang belum ada: ' . implode(', ', $kolomTidakAda));
            }

            $hasil = [];
            $nomorBaris = 1;

            while (($baris = fgetcsv($file, null, ',', '"', '')) !== false) {
                $nomorBaris++;

                if (count($baris) === 1 && trim((string) ($baris[0] ?? '')) === '') {
                    continue;
                }

                if (count($baris) !== count($header)) {
                    throw new RuntimeException("Jumlah kolom tidak sesuai pada baris {$nomorBaris} file {$path}.");
                }

                $nilai = array_map(static fn ($isi) => trim((string) ($isi ?? '')), $baris);
                $hasil[] = array_combine($header, $nilai);
            }

            if ($hasil === []) {
                throw new RuntimeException("CSV tidak memiliki baris data: {$path}");
            }

            return $hasil;
        } finally {
            fclose($file);
        }
    }
}