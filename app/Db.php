<?php
declare(strict_types=1);

final class Db
{
    public static function config(): array
    {
        $path = 'C:/xampp/chayanne_config/config.php';
        if (!is_file($path)) {
            throw new RuntimeException('No se encontro config.php en C:/xampp/chayanne_config/');
        }

        $cfg = require $path;
        if (!is_array($cfg) || !isset($cfg['db'])) {
            throw new RuntimeException('config.php no tiene seccion db valida.');
        }

        return $cfg;
    }

    public static function pdo(): PDO
    {
        $cfg = self::config();
        $db = $cfg['db'];

        $host = (string)($db['host'] ?? '');
        $port = (int)($db['port'] ?? 3306);
        $name = (string)($db['name'] ?? '');
        $user = (string)($db['user'] ?? '');
        $pass = (string)($db['pass'] ?? '');
        $charset = (string)($db['charset'] ?? 'utf8mb4');

        if ($host === '' || $name === '' || $user === '') {
            throw new RuntimeException('Faltan credenciales db en config.php.');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $opt);
    }

    public static function tableExists(PDO $pdo, string $schema, string $table): bool
    {
        $sql = 'SELECT 1
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :s AND TABLE_NAME = :t
                LIMIT 1';
        $st = $pdo->prepare($sql);
        $st->execute([':s' => $schema, ':t' => $table]);
        return (bool)$st->fetchColumn();
    }

    public static function getColumns(PDO $pdo, string $schema, string $table): array
    {
        $sql = 'SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = :s AND TABLE_NAME = :t
                ORDER BY ORDINAL_POSITION';
        $st = $pdo->prepare($sql);
        $st->execute([':s' => $schema, ':t' => $table]);
        $out = [];
        foreach ($st->fetchAll() as $r) {
            $out[] = (string)$r['COLUMN_NAME'];
        }
        return $out;
    }
}