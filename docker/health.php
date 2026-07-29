<?php
/**
 * Endpoint de salud para el smoke test del despliegue CONAF.
 *
 * Moodle no trae uno propio, y el workflow de infra-docker-base espera un
 * GET /health que responda 200: sin esto el despliegue se marca como fallido
 * aunque el sitio esté perfectamente bien.
 *
 * No arranca Moodle entero. ABORT_AFTER_CONFIG hace que lib/setup.php cargue
 * $CFG y se detenga ahí, sin conectar a la base ni levantar sesiones — así el
 * chequeo es barato y no ensucia nada. La conexión se prueba aparte, a mano.
 */

define('ABORT_AFTER_CONFIG', true);
define('NO_MOODLE_COOKIES', true);
define('NO_UPGRADE_CHECK', true);

require(__DIR__ . '/config.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$estado = array('status' => 'ok');
$problemas = array();

// 1. moodledata: montado y escribible por el usuario del contenedor.
if (!is_dir($CFG->dataroot)) {
    $problemas[] = 'dataroot no existe';
} else if (!is_writable($CFG->dataroot)) {
    $problemas[] = 'dataroot no escribible (revisar chown 33:33 en el host)';
}
$estado['dataroot'] = empty($problemas) ? 'ok' : 'error';

// 2. Base de datos: una conexión y una consulta trivial, con timeout corto.
//    Nunca se devuelve el detalle del error al cliente: expondría credenciales
//    o la topología interna. El detalle va al log del contenedor.
$estado['db'] = 'error';
try {
    if ($CFG->dbtype === 'pgsql') {
        $puerto = empty($CFG->dboptions['dbport']) ? 5432 : (int)$CFG->dboptions['dbport'];
        $cadena = sprintf(
            "host=%s port=%d dbname=%s user=%s password=%s connect_timeout=3",
            $CFG->dbhost, $puerto, $CFG->dbname, $CFG->dbuser, $CFG->dbpass
        );
        $conexion = @pg_connect($cadena);
        if ($conexion && @pg_query($conexion, 'SELECT 1')) {
            $estado['db'] = 'ok';
        }
        if ($conexion) {
            pg_close($conexion);
        }
    } else {
        $puerto = empty($CFG->dboptions['dbport']) ? 3306 : (int)$CFG->dboptions['dbport'];
        $mysqli = @mysqli_init();
        @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        if (@mysqli_real_connect($mysqli, $CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $puerto)) {
            if (@mysqli_query($mysqli, 'SELECT 1')) {
                $estado['db'] = 'ok';
            }
            @mysqli_close($mysqli);
        }
    }
} catch (Throwable $e) {
    error_log('health: fallo de conexion a la base: ' . $e->getMessage());
}

if ($estado['db'] !== 'ok') {
    $problemas[] = 'sin conexion a la base de datos';
}

if ($problemas) {
    $estado['status'] = 'error';
    $estado['detalle'] = $problemas;
    http_response_code(503);
}

echo json_encode($estado);
