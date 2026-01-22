<?php
declare(strict_types=1);

final class Layout
{
    public static function header(string $title, string $active = ''): void
    {
        $base = self::baseUrl();
        $css = $base . '/assets/app.css';
        $logo = $base . '/assets/logo.jpg';

        echo '<!doctype html><html lang="es"><head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>' . Util::h($title) . '</title>';
        echo '<link rel="stylesheet" href="' . Util::h($css) . '">';
        echo '</head><body><div class="container">';

        echo '<div class="topbar">';
        echo '<div class="brand">';
        echo '<img src="' . Util::h($logo) . '" alt="Logo">';
        echo '<div><h1>Chayanne</h1><p>Preview SEC-STAR (solo lectura)</p></div>';
        echo '</div>';

        echo '<div class="nav">';
        self::navLink($base . '/index.php', 'Inicio', $active === 'home');
        self::navLink($base . '/upload.php', 'Subida individual', $active === 'single');
        self::navLink($base . '/batch.php', 'Subida masiva', $active === 'batch');
        self::navLink($base . '/health.php', 'Salud', $active === 'health');
        echo '</div>';

        echo '</div>';
    }

    public static function footer(): void
    {
        echo '<div class="footer">LuzParral - Piloto Chayanne. Sin inserciones en BD.</div>';
        echo '</div></body></html>';
    }

    private static function navLink(string $href, string $text, bool $isActive): void
    {
        $cls = $isActive ? 'active' : '';
        echo '<a class="' . $cls . '" href="' . Util::h($href) . '">' . Util::h($text) . '</a>';
    }

    private static function baseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        if ($dir === '') {
            $dir = '/';
        }
        return $scheme . '://' . $host . $dir;
    }
}