<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/app/Util.php';
require_once __DIR__ . '/app/HeaderOverrides.php';

function fail(string $msg): void
{
    echo '<h1>Encabezados</h1>';
    echo '<p style="color:#b00020;">' . Util::h($msg) . '</p>';
    echo '<p><a href="index.php">Home</a></p>';
    exit;
}

$entity = (string)($_POST['entity'] ?? '');
$expectedCols = (int)($_POST['expected_cols'] ?? 0);
$raw = (string)($_POST['headers_raw'] ?? '');

if ($entity === '' || $expectedCols <= 0) {
    fail('Invalid request.');
}

$raw = trim($raw);
if ($raw === '') {
    fail('Headers are required.');
}

$raw = str_replace(["\r\n", "\r"], "\n", $raw);
$parts = preg_split('/,|\n/', $raw);
if (!is_array($parts)) {
    fail('Could not parse headers.');
}

$headers = [];
foreach ($parts as $p) {
    $p = trim((string)$p);
    if ($p !== '') {
        $headers[] = $p;
    }
}

if (count($headers) !== $expectedCols) {
    fail('Header count mismatch. Expected ' . $expectedCols . ' but got ' . count($headers) . '.');
}

if (!HeaderOverrides::save($entity, $headers)) {
    fail('Could not save override. Check permissions for overrides folder.');
}

$_SESSION['flash'] = 'Header override saved for entity: ' . $entity;

header('Location: preview.php?from_session=1');
exit;