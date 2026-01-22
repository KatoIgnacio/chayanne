<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

Layout::header('Chayanne - Subida individual', 'single');

echo '<div class="hero">';
echo '<h2>Subida individual</h2>';
echo '<p>Selecciona un archivo .txt para ver su vista previa.</p>';
echo '</div>';

echo '<div class="card">';
echo '<form method="post" action="preview.php" enctype="multipart/form-data">';
echo '<input type="file" name="txtfile" accept=".txt" required>';
echo '<div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">';
echo '<button class="btn" type="submit">Ver preview</button>';
echo '<a class="btn secondary" href="index.php">Inicio</a>';
echo '</div>';
echo '</form>';
echo '</div>';

Layout::footer();