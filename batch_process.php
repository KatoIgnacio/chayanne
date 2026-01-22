<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

Layout::header('Chayanne - Resultado del lote', 'batch');

echo '<div class="hero">';
echo '<h2>Resultado del lote</h2>';
echo '<p class="small">Se guardan en storage/uploads y luego puedes abrir preview por archivo.</p>';
echo '</div>';

if (!isset($_FILES['txtfiles']) || !is_array($_FILES['txtfiles'])) {
    echo '<div class="alert err">No hay archivos subidos.</div>';
    echo '<a class="btn secondary" href="batch.php">Atras</a>';
    Layout::footer();
    exit;
}

$uploadsDir = __DIR__ . '/storage/uploads';
Util::ensureDir($uploadsDir);

$names = $_FILES['txtfiles']['name'] ?? [];
$tmp = $_FILES['txtfiles']['tmp_name'] ?? [];
$err = $_FILES['txtfiles']['error'] ?? [];

$lote = date('Ymd_His');

echo '<div class="card">';
echo '<div class="tablewrap"><table><thead><tr>';
echo '<th>Archivo</th><th>Entidad</th><th>Estado</th>';
echo '</tr></thead><tbody>';

for ($i = 0; $i < count($names); $i++) {
    $n = (string)($names[$i] ?? '');
    $t = (string)($tmp[$i] ?? '');
    $e = (int)($err[$i] ?? UPLOAD_ERR_NO_FILE);

    if ($n === '') {
        continue;
    }

    $entity = Util::detectEntityFromFilename($n);
    $status = 'OK';

    if ($e !== UPLOAD_ERR_OK) {
        $status = 'Error subida: ' . (string)$e;
    } elseif (!Util::isTxtFilename($n)) {
        $status = 'No es .txt';
    } else {
        $destFile = $lote . '__' . Util::safeBasename($n);
        $destPath = $uploadsDir . '/' . $destFile;
        if (!move_uploaded_file($t, $destPath)) {
            $status = 'No se pudo mover';
        } else {
            $status = 'Guardado (' . Util::h($destFile) . ')';
        }
    }

    echo '<tr>';
    echo '<td>' . Util::h($n) . '</td>';
    echo '<td>' . Util::h($entity === '' ? '-' : $entity) . '</td>';
    echo '<td>' . Util::h($status) . '</td>';
    echo '</tr>';
}

echo '</tbody></table></div>';
echo '<div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">';
echo '<a class="btn secondary" href="batch.php">Atras</a>';
echo '<a class="btn secondary" href="index.php">Inicio</a>';
echo '</div>';
echo '</div>';

Layout::footer();