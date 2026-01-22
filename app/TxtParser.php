<?php
declare(strict_types=1);

final class TxtParser
{
    public static function detectColumnCount(string $path): int
    {
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new RuntimeException('No se pudo abrir el archivo.');
        }

        try {
            while (($line = fgets($fh)) !== false) {
                $line = Util::normalizeNewlines($line);
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parts = str_getcsv($line, ',', '"', '\\');
                return is_array($parts) ? count($parts) : 0;
            }
        } finally {
            fclose($fh);
        }

        return 0;
    }

    public static function parseFile(string $path, int $maxRows): array
    {
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new RuntimeException('No se pudo abrir el archivo.');
        }

        $rows = [];
        try {
            while (($line = fgets($fh)) !== false) {
                if (count($rows) >= $maxRows) {
                    break;
                }
                $line = Util::normalizeNewlines($line);
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parts = str_getcsv($line, ',', '"', '\\');
                if (!is_array($parts) || count($parts) === 0) {
                    continue;
                }
                $rows[] = $parts;
            }
        } finally {
            fclose($fh);
        }

        return $rows;
    }
}