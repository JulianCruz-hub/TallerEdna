<?php
/**
 * Conexion MySQL y datos iniciales del taller.
 
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'taller_edna';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Error de conexion MySQL (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
}

try {
    $mysqli->select_db($DB_NAME);
} catch (mysqli_sql_exception $e) {
    $mysqli->query("CREATE DATABASE `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysqli->select_db($DB_NAME);
}

$mysqli->set_charset('utf8mb4');

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('tabla_vacia')) {
    function tabla_vacia(mysqli $mysqli, string $tabla): bool
    {
        try {
            $res = $mysqli->query("SELECT 1 FROM `$tabla` LIMIT 1");
            return $res->num_rows === 0;
        } catch (mysqli_sql_exception $e) {
            return true;
        }
    }
}

if (!function_exists('column_exists')) {
    function column_exists(mysqli $mysqli, string $table, string $column): bool
    {
        $res = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $res && $res->num_rows > 0;
    }
}

if (!function_exists('index_exists')) {
    function index_exists(mysqli $mysqli, string $table, string $index): bool
    {
        $res = $mysqli->query("SHOW INDEX FROM `$table` WHERE Key_name = '$index'");
        return $res && $res->num_rows > 0;
    }
}


if (!function_exists('normalize_login_key')) {
    function normalize_login_key(string $value): string
    {
        $value = trim(mb_strtolower($value, 'UTF-8'));
        if (class_exists('Normalizer')) {
            $value = Normalizer::normalize($value, Normalizer::FORM_D);
            $value = preg_replace('/\p{M}/u', '', $value);
        }

        $ascii = iconv('UTF-8', 'ASCII//IGNORE', $value);
        if ($ascii !== false) {
            $value = $ascii;
        }

        return preg_replace('/[^a-z0-9]/', '', $value);
    }
}

if (!function_exists('email_local_part_from_name')) {
    function email_local_part_from_name(string $name, int $id = 0): string
    {
        $key = normalize_login_key($name);
        return $key !== '' ? $key : 'cliente' . max(1, $id);
    }
}

if (!function_exists('default_client_email')) {
    function default_client_email(string $name, int $id = 0): string
    {
        return email_local_part_from_name($name, $id) . '@ednamoda.com';
    }
}

if (!function_exists('default_client_password_plain')) {
    function default_client_password_plain(int $id = 0): string
    {
        return str_pad((string)max(1, $id), 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('client_password_matches')) {
    function client_password_matches(string $plain, array $cliente): bool
    {
        $hash = (string)($cliente['password_hash'] ?? '');
        if ($hash === '') {
            return false;
        }

        if (password_verify($plain, $hash)) {
            return true;
        }

        $id = (int)($cliente['id'] ?? 0);
        $desired = default_client_password_plain($id);
        $legacy = (string)max(1, $id);

        if (password_verify($desired, $hash) && ($plain === $desired || $plain === $legacy)) {
            return true;
        }

        if (password_verify($legacy, $hash) && $plain === $legacy) {
            return true;
        }

        return false;
    }
}

if (!function_exists('resolve_login_email')) {
    function resolve_login_email(string $input): string
    {
        $input = trim(mb_strtolower($input, 'UTF-8'));
        if ($input === '') {
            return '';
        }

        if (strpos($input, '@') === false) {
            return email_local_part_from_name($input) . '@ednamoda.com';
        }

        return $input;
    }
}

if (!function_exists('find_cliente_by_email')) {
    function find_cliente_by_email(mysqli $mysqli, string $input): ?array
    {
        $loginEmail = resolve_login_email($input);
        if ($loginEmail === '') {
            return null;
        }

        $stmt = $mysqli->prepare('SELECT id, nombre, email, password_hash FROM clientes WHERE LOWER(email) = ? LIMIT 1');
        $stmt->bind_param('s', $loginEmail);
        $stmt->execute();
        $cliente = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($cliente) {
            return $cliente;
        }

        $needle = normalize_login_key(strtok($loginEmail, '@') ?: $loginEmail);
        $rows = $mysqli->query('SELECT id, nombre, email, password_hash FROM clientes')->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $local = normalize_login_key(strtok((string)$row['email'], '@') ?: '');
            if ($local !== '' && $local === $needle) {
                return $row;
            }
        }

        return null;
    }
}

// Tablas: clientes, trajes, talleres y citas.
$ddl = [
    "CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(120) NOT NULL,
        email VARCHAR(190) DEFAULT NULL,
        password_hash VARCHAR(255) DEFAULT NULL,
        superpoder VARCHAR(200),
        colores VARCHAR(150)
    )",
    "CREATE TABLE IF NOT EXISTS trajes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        cliente_id INT,
        estado ENUM('diseno','costura','taller') NOT NULL DEFAULT 'diseno',
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
    )",
    "CREATE TABLE IF NOT EXISTS talleres (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sala VARCHAR(100) NOT NULL,
        tipo ENUM('diseno','costura','pruebas') NOT NULL DEFAULT 'diseno'
    )",
    "CREATE TABLE IF NOT EXISTS citas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        traje_id INT,
        taller_id INT,
        dia DATE NOT NULL,
        hora TIME NOT NULL,
        duracion_horas INT NOT NULL DEFAULT 1,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
        FOREIGN KEY (traje_id) REFERENCES trajes(id) ON DELETE SET NULL,
        FOREIGN KEY (taller_id) REFERENCES talleres(id) ON DELETE SET NULL,
        CHECK (MINUTE(hora) = 0 AND SECOND(hora) = 0)
    )"
];

foreach ($ddl as $sql) {
    $mysqli->query($sql);
}

// Columnas de acceso anadidas en instalaciones antiguas.
if (!column_exists($mysqli, 'clientes', 'email')) {
    $mysqli->query("ALTER TABLE clientes ADD COLUMN email VARCHAR(190) DEFAULT NULL AFTER nombre");
}

if (!column_exists($mysqli, 'clientes', 'password_hash')) {
    $mysqli->query("ALTER TABLE clientes ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL AFTER email");
}

if (!index_exists($mysqli, 'clientes', 'ux_clientes_email')) {
    $mysqli->query("ALTER TABLE clientes ADD UNIQUE KEY ux_clientes_email (email)");
}

if (tabla_vacia($mysqli, 'clientes')) {
    $seedClientes = [
        ['Mr. Increíble', 'Superfuerza', 'Rojo, Negro'],
        ['Elastigirl', 'Elasticidad total', 'Rojo, Naranja'],
        ['Violeta', 'Invisibilidad, Campo de fuerza', 'Violeta, Negro'],
        ['Dash', 'Supervelocidad', 'Rojo, Naranja'],
        ['Jack-Jack', 'Poderes múltiples', 'Rojo, Amarillo'],
        ['Frozone', 'Control del hielo', 'Azul, Blanco'],
        ['Edna Moda', 'Genio del diseño', 'Negro'],
        ['Síndrome', 'Tecnología avanzada', 'Naranja, Negro'],
        ['Screenslaver', 'Hipnosis tecnológica', 'Negro, Verde'],
        ['Bomb Voyage', 'Explosivos', 'Negro, Blanco'],
    ];

    $stmt = $mysqli->prepare('INSERT INTO clientes (nombre, superpoder, colores) VALUES (?, ?, ?)');
    foreach ($seedClientes as $c) {
        $stmt->bind_param('sss', $c[0], $c[1], $c[2]);
        $stmt->execute();
    }
    $stmt->close();
}

if (tabla_vacia($mysqli, 'talleres')) {
    $mysqli->query("INSERT INTO talleres (sala, tipo)
    SELECT * FROM (
      SELECT 'Milan', 'diseno' UNION ALL
      SELECT 'Paris', 'costura' UNION ALL
      SELECT 'Madrid', 'pruebas' UNION ALL
      SELECT 'Tokio', 'diseno'
    ) AS tmp");
}

/* TRAJES */

if (tabla_vacia($mysqli, 'trajes')) {
    $seedTrajes = [
        ['Traje Principal - Mr. Increíble', 1, 'taller'],
        ['Traje Principal - Elastigirl', 2, 'costura'],
        ['Traje Gala - Elastigirl', 2, 'diseno'],
        ['Traje Principal - Violeta', 3, 'diseno'],
        ['Traje Velocidad - Dash', 4, 'costura'],
        ['Traje Multiforme - Jack-Jack', 5, 'diseno'],
        ['Traje Hielo - Frozone', 6, 'costura'],
        ['Traje Iconico - Edna Moda', 7, 'diseno'],
        ['Armadura - Síndrome', 8, 'taller'],
        ['Traje Hipnosis - Screenslaver', 9, 'diseno'],
        ['Traje Explosivo - Bomb Voyage', 10, 'taller'],
    ];

    $checkCliente = $mysqli->prepare('SELECT 1 FROM clientes WHERE id = ? LIMIT 1');
    $insertTraje = $mysqli->prepare('INSERT INTO trajes (nombre, cliente_id, estado) VALUES (?, ?, ?)');

    foreach ($seedTrajes as $traje) {
        [$nombreTraje, $clienteId, $estado] = $traje;
        $checkCliente->bind_param('i', $clienteId);
        $checkCliente->execute();
        if ($checkCliente->get_result()->num_rows === 0) {
            continue;
        }

        $insertTraje->bind_param('sis', $nombreTraje, $clienteId, $estado);
        $insertTraje->execute();
    }

    $checkCliente->close();
    $insertTraje->close();
}



// Completa email/clave demo (0001, 0002...) sin pisar registros con contrasena propia.
$rows = $mysqli->query('SELECT id, nombre, email, password_hash FROM clientes ORDER BY id')->fetch_all(MYSQLI_ASSOC);
$updateEmail = $mysqli->prepare('UPDATE clientes SET email = ? WHERE id = ?');
$updatePassword = $mysqli->prepare('UPDATE clientes SET password_hash = ? WHERE id = ?');
$updateBoth = $mysqli->prepare('UPDATE clientes SET email = ?, password_hash = ? WHERE id = ?');

    foreach ($rows as $row) {
    $id = (int)$row['id'];
    $desiredEmail = default_client_email($row['nombre'], $id);
    $currentEmail = trim((string)($row['email'] ?? ''));
    $currentHash = trim((string)($row['password_hash'] ?? ''));

    $hasCustomPassword = $currentHash !== ''
        && !password_verify(default_client_password_plain($id), $currentHash)
        && !password_verify((string)max(1, $id), $currentHash);

    if ($currentEmail === '' && $currentHash === '') {
        $desiredPasswordHash = password_hash(default_client_password_plain($id), PASSWORD_DEFAULT);
        $updateBoth->bind_param('ssi', $desiredEmail, $desiredPasswordHash, $id);
        $updateBoth->execute();
    } elseif ($currentEmail === '') {
        $updateEmail->bind_param('si', $desiredEmail, $id);
        $updateEmail->execute();
    } elseif ($currentHash === '') {
        $desiredPasswordHash = password_hash(default_client_password_plain($id), PASSWORD_DEFAULT);
        $updatePassword->bind_param('si', $desiredPasswordHash, $id);
        $updatePassword->execute();
    } elseif (!$hasCustomPassword && $currentEmail !== $desiredEmail) {
        $updateEmail->bind_param('si', $desiredEmail, $id);
        $updateEmail->execute();
    } elseif ($hasCustomPassword && $currentEmail !== $desiredEmail) {
        $currentLocal = normalize_login_key(strtok($currentEmail, '@') ?: '');
        $desiredLocal = normalize_login_key(strtok($desiredEmail, '@') ?: '');
        if ($currentLocal !== '' && $currentLocal === $desiredLocal) {
            $updateEmail->bind_param('si', $desiredEmail, $id);
            $updateEmail->execute();
        }
    }

    if ($id <= 10) {
        if ($currentEmail !== $desiredEmail) {
            $updateEmail->bind_param('si', $desiredEmail, $id);
            $updateEmail->execute();
        }
        if (!password_verify(default_client_password_plain($id), $currentHash)) {
            $desiredPasswordHash = password_hash(default_client_password_plain($id), PASSWORD_DEFAULT);
            $updatePassword->bind_param('si', $desiredPasswordHash, $id);
            $updatePassword->execute();
        }
    }
}

$updateEmail->close();
$updatePassword->close();
$updateBoth->close();
