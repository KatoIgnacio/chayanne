<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

function fail_page(string $msg): void
{
    Layout::header('Chayanne - Avance', 'single');
    echo '<div class="hero"><h2>Avance</h2><p class="small">Revision y previsualizacion</p></div>';
    echo '<div class="alert err">' . Util::h($msg) . '</div>';
    echo '<a class="btn secondary" href="upload.php">Atras</a>';
    Layout::footer();
    exit;
}

if (!isset($_FILES['txtfile']) || !is_array($_FILES['txtfile'])) {
    fail_page('No se subio archivo.');
}

$f = $_FILES['txtfile'];
if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    fail_page('Error de subida: ' . (string)($f['error'] ?? 'unknown'));
}

$originalName = (string)($f['name'] ?? '');
$tmpPath = (string)($f['tmp_name'] ?? '');
if ($originalName === '' || $tmpPath === '') {
    fail_page('Payload invalido.');
}

if (!Util::isTxtFilename($originalName)) {
    fail_page('El archivo debe ser .txt');
}

$entity = Util::detectEntityFromFilename($originalName);
if ($entity === '') {
    $entity = 'desconocida';
}

$uploadsDir = __DIR__ . '/storage/uploads';
Util::ensureDir($uploadsDir);

$destFile = date('Ymd_His') . '__' . Util::safeBasename($originalName);
$destPath = $uploadsDir . '/' . $destFile;

if (!move_uploaded_file($tmpPath, $destPath)) {
    fail_page('No se pudo mover el archivo subido.');
}

try {
    $detectedCols = TxtParser::detectColumnCount($destPath);
    if ($detectedCols <= 0) {
        fail_page('No se pudo detectar cantidad de columnas.');
    }
    $rows = TxtParser::parseFile($destPath, 200);
} catch (Throwable $e) {
    fail_page('Error parseando: ' . $e->getMessage());
}

$columns = [];
if (EntityMap::has($entity)) {
    $columns = EntityMap::getColumns($entity);
}

if (count($columns) !== $detectedCols) {
    $columns = [];
    for ($i = 1; $i <= $detectedCols; $i++) {
        $columns[] = 'col_' . $i;
    }
}

Layout::header('Chayanne - Avance', 'single');

echo '<div class="hero">';
echo '<h2>Avance</h2>';
echo '<p>Archivo: <b>' . Util::h($originalName) . '</b></p>';
echo '<p class="small">Entidad detectada: ' . Util::h($entity) . ' | Columnas: ' . Util::h((string)$detectedCols) . ' | Filas vista previa: ' . Util::h((string)count($rows)) . '</p>';
echo '</div>';

echo '<div class="card">';
echo '<h3>Vista previa</h3>';
echo '<div class="tablewrap"><table><thead><tr>';
foreach ($columns as $c) {
    echo '<th>' . Util::h($c) . '</th>';
}
echo '</tr></thead><tbody>';

foreach ($rows as $r) {
    echo '<tr>';
    for ($i = 0; $i < $detectedCols; $i++) {
        $val = $r[$i] ?? '';
        echo '<td>' . Util::h((string)$val) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table></div>';
echo '<div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">';
echo '<a class="btn secondary" href="upload.php">Atras</a>';
echo '<a class="btn secondary" href="index.php">Inicio</a>';
echo '</div>';
echo '</div>';

Layout::footer();