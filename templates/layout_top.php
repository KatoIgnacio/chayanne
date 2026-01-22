<?php
declare(strict_types=1);

if (!isset($pageTitle) || !is_string($pageTitle)) {
    $pageTitle = "Chayanne";
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand">
        <img src="assets/img/logo.jpg" alt="LuzParral">
        <div class="title">
          <strong>Chayanne</strong>
          <span>Preview SEC-STAR (solo lectura)</span>
        </div>
      </div>
      <div class="nav">
        <a class="pill" href="index.php">Inicio</a>
        <a class="pill" href="upload.php">Subida individual</a>
        <a class="pill" href="batch.php">Subida masiva</a>
        <a class="pill" href="health.php">Salud</a>
      </div>
    </div>