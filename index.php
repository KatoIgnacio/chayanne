<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

Layout::header('Chayanne - Inicio', 'home');

echo '<div class="hero">';
echo '<h2>Inicio</h2>';
echo '<p>Sube archivos .txt SEC/STAR para previsualizarlos con encabezados, sin escribir en base de datos.</p>';
echo '</div>';

echo '<div class="card">';
echo '<a class="btn" href="upload.php">Subida individual</a> ';
echo '<a class="btn secondary" href="batch.php">Subida masiva</a> ';
echo '<a class="btn secondary" href="health.php">Salud</a>';
echo '</div>';

Layout::footer();