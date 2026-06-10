<?php
/**
 * Alta de nuevo cliente desde Formu.html.
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../html/Formu.html');
}

function back_to_form(array $params): void
{
    redirect('../html/Formu.html?' . http_build_query($params));
}

$nombre = trim((string)($_POST['nombre-super'] ?? ''));
$superpoder = trim((string)($_POST['superpoder'] ?? ''));
$colores = trim((string)($_POST['colores'] ?? ''));
$trajeNombre = trim((string)($_POST['traje-nombre'] ?? ''));
$passwordPlain = (string)($_POST['passwd'] ?? '');
$passwordConfirm = (string)($_POST['passwd_confirm'] ?? '');

if ($nombre === '' || $superpoder === '' || $colores === '' || $trajeNombre === '' || $passwordPlain === '' || $passwordConfirm === '') {
    back_to_form([
        'status' => 'error',
        'message' => 'Completa todos los campos del formulario.',
    ]);
}

if ($passwordPlain !== $passwordConfirm) {
    back_to_form([
        'status' => 'error',
        'message' => 'Las contrasenas no coinciden.',
    ]);
}

if (strlen($passwordPlain) < 4) {
    back_to_form([
        'status' => 'error',
        'message' => 'La contrasena debe tener al menos 4 caracteres.',
    ]);
}

// Cliente + traje se guardan juntos o no se guarda nada.
try {
    $mysqli->begin_transaction();

    $stmt = $mysqli->prepare('INSERT INTO clientes (nombre, superpoder, colores) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $nombre, $superpoder, $colores);
    $stmt->execute();
    $clienteId = (int)$mysqli->insert_id;
    $stmt->close();

    $email = default_client_email($nombre, $clienteId);
    $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare('UPDATE clientes SET email = ?, password_hash = ? WHERE id = ?');
    $stmt->bind_param('ssi', $email, $passwordHash, $clienteId);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare('INSERT INTO trajes (nombre, cliente_id) VALUES (?, ?)');
    $stmt->bind_param('si', $trajeNombre, $clienteId);
    $stmt->execute();
    $stmt->close();

    $mysqli->commit();

    back_to_form([
        'status' => 'ok',
        'message' => 'Registro guardado correctamente. Ya puedes iniciar sesion con tu acceso.',
        'email' => $email,
    ]);
} catch (Throwable $e) {
    $mysqli->rollback();

    $message = $e->getMessage();
    if (stripos($message, 'Duplicate entry') !== false) {
        $message = 'Ya existe un cliente con ese acceso generado. Prueba con un nombre distinto.';
    }

    back_to_form([
        'status' => 'error',
        'message' => $message,
    ]);
}
