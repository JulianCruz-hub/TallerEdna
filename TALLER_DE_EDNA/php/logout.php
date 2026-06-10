<?php
/** Cierra la sesion y redirige al login. */
require_once __DIR__ . '/session_init.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
app_redirect('../html/Login.html');
