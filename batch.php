<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

Layout::header('Chayanne - Subida masiva', 'batch');

echo '<div class="hero">';
echo '<h2>Subida masiva</h2>';
echo '<p>Selecciona varios .txt para validar y abrir preview individualmente.</p>';
echo '</div>';

echo '<div class="card">';
echo '<form method="post" action="batch_process.php" enctype="multipart/form-data">';
echo '<input type="file" name="txtfiles[]" multiple accept=".txt" required>';
echo '<div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">';
echo '<button class="btn" type="submit">Procesar lote</button>';
echo '<a class="btn secondary" href="index.php">Inicio</a>';
echo '</div>';
echo '</form>';
echo '</div>';

Layout::footer();