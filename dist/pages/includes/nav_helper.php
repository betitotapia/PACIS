<?php
// nav_helper.php
function current_path(): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    return rtrim($uri, '/');
}

function nav_active(string $needle): string {
    // Marca active si el path contiene el segmento del módulo
    return (strpos(current_path(), $needle) !== false) ? 'active' : '';
}

function nav_open(array $needles): string {
    foreach ($needles as $n) {
        if (strpos(current_path(), $n) !== false) return 'menu-open';
    }
    return '';
}

function nav_circle(string $activeClass): string {
    return ($activeClass === 'active') ? 'bi-record-circle' : 'bi-circle';
}
