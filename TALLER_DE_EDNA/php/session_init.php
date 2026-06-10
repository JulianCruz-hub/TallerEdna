<?php
/**
 * Sesion unificada del taller.
 */

if (!function_exists('app_redirect')) {
    function app_redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_name('TALLER_EDNA_SESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
