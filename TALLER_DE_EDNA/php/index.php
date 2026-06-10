<?php
/**
 * API JSON unificada del taller.
 */
ob_start();
require_once __DIR__ . '/session_init.php';
require_once __DIR__ . '/db.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function sanitize($value)
{
    return trim((string)($value ?? ''));
}

function json_response(array $payload, int $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function fetch_all_assoc(mysqli $mysqli, string $sql): array
{
    $result = $mysqli->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$area = sanitize($_GET['area'] ?? $_POST['area'] ?? '');

// Area administrador: 
if ($area === 'admin') {

if (empty($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    json_response(['ok' => false, 'message' => 'No autenticado.'], 401);
}

// GET: devuelve todos los datos para las tablas del panel.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $clientes = fetch_all_assoc($mysqli, 'SELECT id, nombre, email, superpoder, colores FROM clientes ORDER BY id DESC');
    $talleres = fetch_all_assoc($mysqli, 'SELECT id, sala, tipo FROM talleres ORDER BY id DESC');
    $trajes = fetch_all_assoc($mysqli, 'SELECT id, nombre, cliente_id, estado FROM trajes ORDER BY id DESC');
    $citas = fetch_all_assoc($mysqli, "SELECT ci.id, ci.cliente_id, ci.traje_id, ci.taller_id, ci.dia, TIME_FORMAT(ci.hora, '%H:%i') AS hora, ci.duracion_horas, c.nombre AS cliente, t.nombre AS traje, ta.sala AS taller, ta.tipo FROM citas ci LEFT JOIN clientes c ON c.id = ci.cliente_id LEFT JOIN trajes t ON t.id = ci.traje_id LEFT JOIN talleres ta ON ta.id = ci.taller_id ORDER BY ci.dia DESC, ci.hora DESC");

    json_response(['ok' => true, 'clientes' => $clientes, 'talleres' => $talleres, 'trajes' => $trajes, 'citas' => $citas]);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // Clientes
        if ($action === 'create_client' || $action === 'update_client') {
            $nombre = sanitize($_POST['nombre']);
            $superpoder = sanitize($_POST['superpoder']);
            $colores = sanitize($_POST['colores']);

            if ($nombre === '') {
                throw new Exception('El nombre del cliente es obligatorio.');
            }

            if ($action === 'create_client') {
                $stmt = $mysqli->prepare('INSERT INTO clientes (nombre, superpoder, colores) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $nombre, $superpoder, $colores);
                $stmt->execute();
                $newId = (int)$mysqli->insert_id;
                $stmt->close();

                $email = default_client_email($nombre, $newId);
                $passwordHash = password_hash(default_client_password_plain($newId), PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare('UPDATE clientes SET email = ?, password_hash = ? WHERE id = ?');
                $stmt->bind_param('ssi', $email, $passwordHash, $newId);
                $stmt->execute();
                $stmt->close();

                json_response(['ok' => true, 'message' => 'Cliente creado correctamente.']);
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Cliente no valido.');
            }

            $email = default_client_email($nombre, $id);
            $passwordHash = password_hash(default_client_password_plain($id), PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('UPDATE clientes SET nombre = ?, email = ?, password_hash = ?, superpoder = ?, colores = ? WHERE id = ?');
            $stmt->bind_param('sssssi', $nombre, $email, $passwordHash, $superpoder, $colores, $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Cliente actualizado correctamente.']);
        }

        if ($action === 'delete_client') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Cliente no válido.');
            }

            $stmt = $mysqli->prepare('DELETE FROM clientes WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Cliente eliminado correctamente.']);
        }

        // Trajes
        if ($action === 'create_traje' || $action === 'update_traje') {
            $nombre = sanitize($_POST['traje_nombre']);
            $clienteId = (int)($_POST['cliente_id'] ?? 0);
            $estado = sanitize($_POST['estado']);
            $validEstados = ['diseno', 'costura', 'taller'];

            if ($nombre === '' || $clienteId <= 0 || !in_array($estado, $validEstados, true)) {
                throw new Exception('Datos del traje incompletos o inválidos.');
            }

            if ($action === 'create_traje') {
                $stmt = $mysqli->prepare('INSERT INTO trajes (nombre, cliente_id, estado) VALUES (?, ?, ?)');
                $stmt->bind_param('sis', $nombre, $clienteId, $estado);
                $stmt->execute();
                $stmt->close();

                json_response(['ok' => true, 'message' => 'Traje creado correctamente.']);
            }

            $id = (int)($_POST['traje_id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Traje no válido.');
            }

            $stmt = $mysqli->prepare('UPDATE trajes SET nombre = ?, cliente_id = ?, estado = ? WHERE id = ?');
            $stmt->bind_param('sisi', $nombre, $clienteId, $estado, $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Traje actualizado correctamente.']);
        }

        if ($action === 'delete_traje') {
            $id = (int)($_POST['traje_id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Traje no válido.');
            }

            $stmt = $mysqli->prepare('DELETE FROM trajes WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Traje eliminado correctamente.']);
        }

        // Talleres
        if ($action === 'create_taller' || $action === 'update_taller') {
            $sala = sanitize($_POST['sala']);
            $tipo = sanitize($_POST['tipo']);
            $validTipos = ['diseno', 'costura', 'pruebas'];

            if ($sala === '' || !in_array($tipo, $validTipos, true)) {
                throw new Exception('Datos del taller incompletos o inválidos.');
            }

            if ($action === 'create_taller') {
                $stmt = $mysqli->prepare('INSERT INTO talleres (sala, tipo) VALUES (?, ?)');
                $stmt->bind_param('ss', $sala, $tipo);
                $stmt->execute();
                $stmt->close();

                json_response(['ok' => true, 'message' => 'Taller creado correctamente.']);
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Taller no válido.');
            }

            $stmt = $mysqli->prepare('UPDATE talleres SET sala = ?, tipo = ? WHERE id = ?');
            $stmt->bind_param('ssi', $sala, $tipo, $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Taller actualizado correctamente.']);
        }

        if ($action === 'delete_taller') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Taller no válido.');
            }

            $stmt = $mysqli->prepare('DELETE FROM talleres WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Taller eliminado correctamente.']);
        }

        // Citas (admin)
        if ($action === 'create_cita' || $action === 'update_cita') {
            $clienteId = (int)($_POST['cita_cliente_id'] ?? 0);
            $trajeId = (int)($_POST['cita_traje_id'] ?? 0);
            $tallerId = (int)($_POST['cita_taller_id'] ?? 0);
            $dia = sanitize($_POST['dia']);
            $hora = sanitize($_POST['hora']);
            $duracion = (int)($_POST['duracion_horas'] ?? 1);

            if ($clienteId <= 0 || $trajeId <= 0 || $tallerId <= 0 || $dia === '' || $hora === '' || $duracion <= 0) {
                throw new Exception('Datos de la cita incompletos o inválidos.');
            }

            $horaSql = strlen($hora) === 5 ? ($hora . ':00') : $hora;

            if ($action === 'create_cita') {
                $stmt = $mysqli->prepare('INSERT INTO citas (cliente_id, traje_id, taller_id, dia, hora, duracion_horas) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('iiissi', $clienteId, $trajeId, $tallerId, $dia, $horaSql, $duracion);
                $stmt->execute();
                $stmt->close();

                json_response(['ok' => true, 'message' => 'Cita creada correctamente.']);
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Cita no válida.');
            }

            $stmt = $mysqli->prepare('UPDATE citas SET cliente_id = ?, traje_id = ?, taller_id = ?, dia = ?, hora = ?, duracion_horas = ? WHERE id = ?');
            $stmt->bind_param('iiissii', $clienteId, $trajeId, $tallerId, $dia, $horaSql, $duracion, $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Cita actualizada correctamente.']);
        }

        if ($action === 'delete_cita') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Cita no válida.');
            }

            $stmt = $mysqli->prepare('DELETE FROM citas WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            json_response(['ok' => true, 'message' => 'Cita eliminada correctamente.']);
        }

        throw new Exception('Acción no reconocida.');
    } catch (Throwable $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }
}

json_response(['ok' => false, 'message' => 'Metodo no permitido.'], 405);

} elseif ($area === 'usuario') {

$clienteId = (int)($_SESSION['cliente_id'] ?? 0);
if ($clienteId <= 0) {
    json_response(['ok' => false, 'message' => 'No autenticado.'], 401);
}

// GET: ficha del cliente logueado, sus trajes, citas y talleres disponibles.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $mysqli->prepare('SELECT id, nombre, email, superpoder, colores FROM clientes WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $clienteId);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$cliente) {
        $_SESSION = [];
        session_destroy();
        json_response(['ok' => false, 'message' => 'Cliente no encontrado.'], 401);
    }

    $stmt = $mysqli->prepare('SELECT id, nombre, estado FROM trajes WHERE cliente_id = ? ORDER BY id');
    $stmt->bind_param('i', $clienteId);
    $stmt->execute();
    $trajes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT c.id, c.dia, TIME_FORMAT(c.hora, '%H:%i') AS hora, c.duracion_horas, t.nombre AS traje, ta.sala, ta.tipo FROM citas c LEFT JOIN trajes t ON t.id = c.traje_id LEFT JOIN talleres ta ON ta.id = c.taller_id WHERE c.cliente_id = ? ORDER BY c.dia, c.hora");
    $stmt->bind_param('i', $clienteId);
    $stmt->execute();
    $citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $talleres = fetch_all_assoc($mysqli, 'SELECT id, sala, tipo FROM talleres ORDER BY sala');

    $estadoInfo = [
        'diseno' => ['label' => 'En diseno', 'text' => 'Edna esta revisando concepto, color y funcionalidad de la prenda.'],
        'costura' => ['label' => 'En costura', 'text' => 'El taller esta ensamblando las piezas y ajustando acabados.'],
        'taller' => ['label' => 'En taller', 'text' => 'El traje esta en fase final de pruebas y retoques.'],
    ];

    json_response([
        'ok' => true,
        'cliente' => $cliente,
        'trajes' => $trajes,
        'citas' => $citas,
        'talleres' => $talleres,
        'estadoInfo' => $estadoInfo,
    ]);
}

// POST: el cliente solo puede crear una cita para sus propios trajes.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action !== 'create_user_cita') {
        json_response(['ok' => false, 'message' => 'Accion no permitida.'], 405);
    }

    try {
        $trajeId = (int)($_POST['traje_id'] ?? 0);
        $tallerId = (int)($_POST['taller_id'] ?? 0);
        $dia = sanitize($_POST['dia']);
        $hora = sanitize($_POST['hora']);
        $duracion = (int)($_POST['duracion_horas'] ?? 1);

        if ($trajeId <= 0 || $tallerId <= 0 || $dia === '' || $hora === '' || $duracion <= 0) {
            throw new Exception('Completa todos los datos de la cita.');
        }

        $fechaCita = DateTime::createFromFormat('Y-m-d', $dia);
        $fechaHoy = new DateTime();
        $fechaHoy->setTime(0, 0, 0);

        if ($fechaCita === false || $fechaCita < $fechaHoy) {
            throw new Exception('La fecha de la cita debe ser igual o posterior a hoy.');
        }

        $stmt = $mysqli->prepare('SELECT id FROM trajes WHERE id = ? AND cliente_id = ? LIMIT 1');
        $stmt->bind_param('ii', $trajeId, $clienteId);
        $stmt->execute();
        $trajePropio = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$trajePropio) {
            throw new Exception('Solo puedes reservar citas para tus propios trajes.');
        }

        $stmt = $mysqli->prepare('SELECT id FROM citas WHERE cliente_id = ? LIMIT 1');
        $stmt->bind_param('i', $clienteId);
        $stmt->execute();
        $yaTieneCita = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($yaTieneCita) {
            throw new Exception('Ya tienes una cita registrada.');
        }

        $horaSql = strlen($hora) === 5 ? $hora . ':00' : $hora;
        $stmt = $mysqli->prepare('INSERT INTO citas (cliente_id, traje_id, taller_id, dia, hora, duracion_horas) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iiissi', $clienteId, $trajeId, $tallerId, $dia, $horaSql, $duracion);
        $stmt->execute();
        $stmt->close();

        json_response(['ok' => true, 'message' => 'Tu cita se ha registrado correctamente.']);
    } catch (Throwable $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }
}

json_response(['ok' => false, 'message' => 'Metodo no permitido.'], 405);

} else {
    json_response(['ok' => false, 'message' => 'Area no valida. Usa area=admin o area=usuario.'], 400);
}
