<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Util.php';
require_once __DIR__ . '/app/EntityMap.php';
require_once __DIR__ . '/app/TxtParser.php';
require_once __DIR__ . '/app/Db.php';

$configPath = 'C:/xampp/chayanne_config/config.php';
$config = file_exists($configPath)
    ? require $configPath
    : ['app' => ['max_preview_rows' => 200], 'db' => []];

$maxRows = (int)($config['app']['max_preview_rows'] ?? 200);

function fail(string $msg): void
{
    echo '<h1>Preview</h1>';
    echo '<p style="color:#b00020;">' . Util::h($msg) . '</p>';
    echo '<p><a href="batch.php">Back</a></p>';
    exit;
}

$batch = (string)($_GET['batch'] ?? '');
$file = (string)($_GET['file'] ?? '');

if ($batch === '' || $file === '') {
    fail('Missing batch or file');
}

$uploadsBase = realpath(__DIR__ . '/storage/uploads');
if ($uploadsBase === false) {
    fail('Uploads base not found');
}

$targetDir = realpath($uploadsBase . DIRECTORY_SEPARATOR . $batch);
if ($targetDir === false) {
    fail('Batch directory not found');
}

$targetPath = realpath($targetDir . DIRECTORY_SEPARATOR . $file);
if ($targetPath === false) {
    fail('File not found');
}

if (strpos($targetPath, $uploadsBase) !== 0) {
    fail('Invalid path');
}

$originalName = basename($targetPath);
$entity = Util::detectEntityFromFilename($originalName);
if ($entity === '') {
    fail('Could not detect entity from filename');
}

$pdo = null;
try {
    if (!empty($config['db']['host'])) {
        $pdo = Db::connect($config);
    }
} catch (Throwable $e) {
    $pdo = null;
}

$expectedCols = EntityMap::expectedColumnCount($entity);
if ($expectedCols <= 0 && $pdo !== null) {
    $expectedCols = EntityMap::expectedColumnCountFromDb($pdo, $config['db']['database'] ?? '', $entity);
}

if ($expectedCols <= 0) {
    fail('Entity has no column definition: ' . $entity);
}

try {
    $firstLineCols = TxtParser::detectColumnCount($targetPath);
    if ($firstLineCols !== $expectedCols) {
        fail('Column mismatch. Expected ' . (string)$expectedCols . ' got ' . (string)$firstLineCols);
    }

    $rows = TxtParser::parseFile($targetPath, $maxRows);
} catch (Throwable $e) {
    fail('Parse error: ' . $e->getMessage());
}

$columns = EntityMap::getColumns($entity);
if (count($columns) === 0 && $pdo !== null) {
    $columns = EntityMap::getColumnsFromDb($pdo, $config['db']['database'] ?? '', $entity);
}

echo '<h1>Preview</h1>';
echo '<p><b>File:</b> ' . Util::h($originalName) . '</p>';
echo '<p><b>Entity:</b> ' . Util::h($entity) . '</p>';
echo '<p><b>Expected columns:</b> ' . Util::h((string)$expectedCols) . '</p>';
echo '<p><b>Preview rows:</b> ' . Util::h((string)count($rows)) . ' (limit ' . Util::h((string)$maxRows) . ')</p>';

echo '<div style="overflow:auto; max-width:100%;">';
echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; white-space:nowrap;">';
echo '<thead><tr>';
foreach ($columns as $col) {
    echo '<th>' . Util::h($col) . '</th>';
}
echo '</tr></thead><tbody>';

foreach ($rows as $r) {
    echo '<tr>';
    for ($i = 0; $i < $expectedCols; $i++) {
        $val = $r[$i] ?? '';
        echo '<td>' . Util::h((string)$val) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table></div>';
echo '<p style="margin-top:16px;"><a href="batch.php">Back</a> | <a href="index.php">Home</a></p>';