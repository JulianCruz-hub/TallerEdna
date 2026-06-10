<?php
/**
 * Autenticacion por formulario POST.
 */
ob_start();
require_once __DIR__ . '/session_init.php';
require_once __DIR__ . '/db.php';
ob_end_clean();

function login_fail(string $message, string $email = ''): void
{
    app_redirect('../html/Login.html?' . http_build_query([
        'error' => $message,
        'email' => $email,
    ]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    app_redirect('../html/Login.html');
}

$email = trim((string)($_POST['email'] ?? $_POST['usuario'] ?? ''));
$password = (string)($_POST['passwd'] ?? '');

if ($email === '' || $password === '') {
    login_fail('Introduce correo y contrasena.', $email);
}

// Acceso administrador
if (strcasecmp($email, 'Admin') === 0) {
    if ($password !== '123') {
        login_fail('Credenciales de administrador incorrectas.', $email);
    }

    unset($_SESSION['cliente_id'], $_SESSION['cliente_nombre'], $_SESSION['cliente_email']);
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_nombre'] = 'Admin';
    app_redirect('../html/admin.html');
}

// Acceso cliente
$cliente = find_cliente_by_email($mysqli, $email);

if (!$cliente || !client_password_matches($password, $cliente)) {
    login_fail('Correo o contrasena incorrectos.', $email);
}

unset($_SESSION['admin_authenticated'], $_SESSION['admin_nombre']);
$_SESSION['cliente_id'] = (int)$cliente['id'];
$_SESSION['cliente_nombre'] = $cliente['nombre'];
$_SESSION['cliente_email'] = $cliente['email'];
app_redirect('../html/usuario.html');
