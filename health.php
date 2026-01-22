<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

Layout::header('Chayanne - Salud', 'health');

echo '<div class="hero">';
echo '<h2>Salud</h2>';
echo '<p>Chequeo de PHP, storage y conexion a Cloud SQL. Tambien verifica existencia de tablas.</p>';
echo '</div>';

echo '<div class="card">';
echo '<div class="alert ok">PHP OK</div>';

$uploadsDir = __DIR__ . '/storage/uploads';
if (Util::ensureDir($uploadsDir)) {
    echo '<div class="alert ok">Storage OK</div>';
} else {
    echo '<div class="alert err">Storage FAIL</div>';
}

try {
    $cfg = Db::config();
    $schema = (string)($cfg['db']['name'] ?? '');
    $pdo = Db::pdo();

    echo '<div class="alert ok">DB OK (Cloud SQL)</div>';

    // --- Filtros por GET ---
    // prefix=nodo_  -> TABLE_NAME LIKE 'nodo\_%'
    // like=nodo\_%  -> TABLE_NAME LIKE 'nodo\_%'
    // q=incidencia  -> TABLE_NAME LIKE '%incidencia%'

    $like = '%';

    $prefix = isset($_GET['prefix']) ? trim((string)$_GET['prefix']) : '';
    $likeParam = isset($_GET['like']) ? trim((string)$_GET['like']) : '';
    $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

    // Escapa caracteres especiales de LIKE: \ % _
    $escapeLike = function (string $s): string {
        $s = str_replace('\\', '\\\\', $s);
        $s = str_replace('%', '\%', $s);
        $s = str_replace('_', '\_', $s);
        return $s;
    };

    if ($prefix !== '') {
        // Queremos prefijo literal con "_" literal, y luego cualquier cosa
        $like = $escapeLike($prefix) . '%';
    } elseif ($likeParam !== '') {
        // Permite pasar un LIKE ya armado (responsabilidad del usuario)
        $like = $likeParam;
    } elseif ($q !== '') {
        $like = '%' . $escapeLike($q) . '%';
    }

    // Total tablas del schema (sin filtro)
    $stAll = $pdo->prepare('SELECT COUNT(*) c
                            FROM INFORMATION_SCHEMA.TABLES
                            WHERE TABLE_SCHEMA = :s');
    $stAll->execute([':s' => $schema]);
    $totalTables = (int)($stAll->fetch()['c'] ?? 0);

    // UI: filtros rapidos + busqueda
    echo '<div style="margin:12px 0; display:flex; gap:8px; flex-wrap:wrap;">';
    echo '<a href="health.php">Todas</a>';
    echo '<a href="health.php?prefix=nodo_">NODO_*</a>';
    echo '<a href="health.php?prefix=punto_">PUNTO_*</a>';
    echo '<a href="health.php?prefix=tramo_">TRAMO_*</a>';
    echo '<a href="health.php?prefix=subestacion_">SUBESTACION_*</a>';
    echo '<a href="health.php?q=incidencia">*INCIDENCIA*</a>';
    echo '</div>';

    echo '<form method="get" action="health.php" style="margin:10px 0;">';
    echo 'Buscar contiene: <input name="q" value="' . Util::h($q) . '" />';
    echo ' <button type="submit">Buscar</button>';
    echo '</form>';

    // Lista tablas segun filtro LIKE y schema actual
    // Nota: no usamos ESCAPE para evitar el error de sintaxis en tu server
    $sqlTables = 'SELECT TABLE_NAME
                  FROM INFORMATION_SCHEMA.TABLES
                  WHERE TABLE_SCHEMA = :s
                    AND TABLE_NAME LIKE :like
                  ORDER BY TABLE_NAME';
    $st = $pdo->prepare($sqlTables);
    $st->execute([':s' => $schema, ':like' => $like]);

    $toCheck = [];
    foreach ($st->fetchAll() as $r) {
        $toCheck[] = (string)$r['TABLE_NAME'];
    }

    $matchedTables = count($toCheck);

    echo '<p><b>Schema:</b> ' . Util::h($schema) . '</p>';
    echo '<p><b>Total tablas en DB:</b> ' . Util::h((string)$totalTables) . '</p>';
    echo '<p><b>Filtro LIKE:</b> ' . Util::h($like) . '</p>';
    echo '<p><b>Tablas que matchean:</b> ' . Util::h((string)$matchedTables) . '</p>';

    echo '<div class="tablewrap"><table><thead><tr>';
    echo '<th>Tabla</th><th>Tabla existe</th><th># Columnas (DB)</th>';
    echo '</tr></thead><tbody>';

    foreach ($toCheck as $t) {
        $exists = Db::tableExists($pdo, $schema, $t);
        $ncols = 0;
        if ($exists) {
            $cols = Db::getColumns($pdo, $schema, $t);
            $ncols = count($cols);
        }

        echo '<tr>';
        echo '<td>' . Util::h($t) . '</td>';
        echo '<td>' . ($exists ? 'SI' : 'NO') . '</td>';
        echo '<td>' . Util::h((string)$ncols) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';

} catch (Throwable $e) {
    echo '<div class="alert err">DB FAIL: ' . Util::h($e->getMessage()) . '</div>';
}

echo '<div style="margin-top:12px;">';
echo '<a class="btn secondary" href="index.php">Inicio</a>';
echo '</div>';

echo '</div>';

Layout::footer();