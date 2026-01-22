<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Db.php';

$configPath = 'C:/xampp/chayanne_config/config.php';
$config = require $configPath;

$pdo = Db::connect($config);

$table = $_GET['table'] ?? 'aviso';

$sql = "
SELECT COLUMN_NAME, ORDINAL_POSITION, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = :schema
  AND TABLE_NAME = :table
ORDER BY ORDINAL_POSITION
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':schema' => $config['db']['database'],
    ':table' => $table,
]);

$cols = $stmt->fetchAll();

echo "<h1>Schema check</h1>";
echo "<p><b>Table:</b> " . htmlspecialchars($table) . "</p>";
echo "<p><b>Column count:</b> " . htmlspecialchars((string)count($cols)) . "</p>";

echo "<h2>Columns</h2>";
echo "<ol>";
foreach ($cols as $c) {
    echo "<li>" . htmlspecialchars($c['COLUMN_NAME']) . " (" . htmlspecialchars($c['DATA_TYPE']) . ", nullable=" . htmlspecialchars($c['IS_NULLABLE']) . ")</li>";
}
echo "</ol>";

echo "<h2>EntityMap snippet</h2>";
echo "<pre>";
echo "'" . htmlspecialchars($table) . "' => [\n";
echo "    'columns' => [\n";
foreach ($cols as $c) {
    echo "        '" . htmlspecialchars($c['COLUMN_NAME']) . "',\n";
}
echo "    ],\n";
echo "],\n";
echo "</pre>";