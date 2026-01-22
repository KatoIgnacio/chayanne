<?php
declare(strict_types=1);

final class Util
{
    public static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function isTxtFilename(string $name): bool
    {
        return (bool)preg_match('/\.txt$/i', $name);
    }

    public static function normalizeNewlines(string $s): string
    {
        $s = str_replace("\r\n", "\n", $s);
        $s = str_replace("\r", "\n", $s);
        return $s;
    }

    public static function safeBasename(string $name): string
    {
        $name = basename($name);
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
        if ($safe === null || $safe === '') {
            return 'upload.txt';
        }
        return $safe;
    }

    public static function ensureDir(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }
        return @mkdir($path, 0777, true);
    }

    public static function detectEntityFromFilename(string $filename): string
    {
        $base = strtoupper(basename($filename));
        $base = preg_replace('/\.TXT$/', '', $base);
        if ($base === null || $base === '') {
            return '';
        }

        // Prefix until first digit
        if (!preg_match('/^([A-Z_]+)\d+$/', $base, $m)) {
            return '';
        }

        return strtolower($m[1]);
    }
}