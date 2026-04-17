<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

// Opcional: evita que warnings rompan tu respuesta AJAX
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

/**
 * Parser GS1/SUMED + soporte para código en 2 partes:
 * - Si llega "01..." guarda referencia en sesión (gs1_ref)
 * - Si luego llega "17..." completa caducidad y lote con esa referencia
 *
 * Siempre regresa llaves: referencia, lote, caducidad
 */
function parse_gs1_like(string $codigo): array {
    $codigo = trim($codigo);
    $out = ['referencia' => '', 'lote' => '', 'caducidad' => ''];

    if ($codigo === '') return $out;

    // 1) GS1 largo: 01(14) 17(6) 10(lote...)
    if (strpos($codigo, '01') === 0 && strlen($codigo) >= 30) {
        $out['referencia'] = ltrim(substr($codigo, 2, 14), '0');

        $cad = substr($codigo, 18, 6);
        if (ctype_digit($cad)) {
            $out['caducidad'] = '20' . substr($cad, 0, 2) . '-' . substr($cad, 2, 2) . '-' . substr($cad, 4, 2);
        }

        $out['lote'] = substr($codigo, 26);
        return $out;
    }

    // 2) SUMED: (según tu lógica previa)
    if (strpos($codigo, '113') === 0 && strlen($codigo) >= 30) {
        $out['referencia'] = ltrim(substr($codigo, 0, 19), '0');

        $cad = substr($codigo, 21, 6);
        if (ctype_digit($cad)) {
            $out['caducidad'] = '20' . substr($cad, 0, 2) . '-' . substr($cad, 2, 2) . '-' . substr($cad, 4, 2);
        }

        $out['lote'] = substr($codigo, 29);
        return $out;
    }

    // 3) Código dividido (primero 01... guarda ref)
    if (strpos($codigo, '01') === 0 && strlen($codigo) >= 4) {
        $out['referencia'] = ltrim(substr($codigo, 2), '0');
        $_SESSION['gs1_ref'] = $out['referencia'];
        return $out;
    }

    // 4) Código dividido (luego 17... usa la ref guardada)
    if (strpos($codigo, '17') === 0 && isset($_SESSION['gs1_ref']) && strlen($codigo) >= 8) {
        $out['referencia'] = (string)$_SESSION['gs1_ref'];

        $cad = substr($codigo, 2, 6);
        if (ctype_digit($cad)) {
            $out['caducidad'] = '20' . substr($cad, 0, 2) . '-' . substr($cad, 2, 2) . '-' . substr($cad, 4, 2);
        }

        // lote desde la pos 8 en adelante (ajústalo si tu “17” incluye separadores)
        $out['lote'] = substr($codigo, 8);
        unset($_SESSION['gs1_ref']);
        return $out;
    }

    // 5) Si no es GS1 reconocible, lo tratamos como referencia directa
    $out['referencia'] = $codigo;
    return $out;
}

/* =========================
   ENTRADAS
   ========================= */

$modo   = isset($_POST['modo']) ? trim($_POST['modo']) : 'PARSE';
$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
$id_oc  = isset($_POST['id_oc']) ? (int)$_POST['id_oc'] : 0;

$id_almacen = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen'] : 0;

// Datos capturados desde modal (GUARDAR)
$ref_capt   = isset($_POST['referencia'])  ? trim($_POST['referencia'])  : '';
$desc_capt  = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$lote_capt  = isset($_POST['lote']) ? trim($_POST['lote']) : '';
$cad_capt   = isset($_POST['caducidad']) ? trim($_POST['caducidad']) : '';
$cantidad   = isset($_POST['cantidad']) ? (float)$_POST['cantidad'] : 0;
$costo      = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;
$exento_iva = isset($_POST['exento_iva']) ? (int)$_POST['exento_iva'] : 0;

if ($codigo === '') { echo "Código vacío"; exit; }
if ($cantidad <= 0) $cantidad = 1;
$exento_iva = ($exento_iva === 1) ? 1 : 0;

/* =========================
   PARSE -> SIEMPRE abre modal
   ========================= */
if ($modo === 'PARSE') {
    $p = parse_gs1_like($codigo);
    if (!is_array($p)) $p = [];

    $ref  = trim($p['referencia'] ?? '');
    $lote = trim($p['lote'] ?? '');
    $cad  = trim($p['caducidad'] ?? '');

    // sugerencias (si existe en products)
    $desc_sug = '';
    $costo_sug = '0';
    $exento_sug = '0';

    if ($ref !== '') {
        $ref_sql = mysqli_real_escape_string($con, $ref);
        $q = mysqli_query($con, "SELECT descripcion, costo, exento_iva FROM products WHERE referencia='$ref_sql' LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $rw = mysqli_fetch_assoc($q);
            $desc_sug = $rw['descripcion'] ?? '';
            $costo_sug = isset($rw['costo']) ? (string)$rw['costo'] : '0';
            $exento_sug = isset($rw['exento_iva']) ? (string)$rw['exento_iva'] : '0';
        }
    }

    // Si viene de OC: sugerir costo desde la OC
    if ($id_oc > 0 && $ref !== '') {
        $ref_sql = mysqli_real_escape_string($con, $ref);
        $qoc = mysqli_query($con, "SELECT costo_unitario FROM ordenes_compra_detalle WHERE id_oc=$id_oc AND referencia='$ref_sql' LIMIT 1");
        if ($qoc && mysqli_num_rows($qoc) > 0) {
            $rw = mysqli_fetch_assoc($qoc);
            if (isset($rw['costo_unitario'])) $costo_sug = (string)$rw['costo_unitario'];
        }
    }

    // Siempre regresamos NECESITA_DATOS para abrir modal SIEMPRE
    echo "NECESITA_DATOS|$ref|$lote|$cad|$desc_sug|$costo_sug|$exento_sug|$codigo";
    exit;
}

/* =========================
   GUARDAR
   ========================= */

if ($id_almacen <= 0) { echo "Selecciona almacén."; exit; }
if ($ref_capt === '') { echo "Falta referencia."; exit; }
if ($desc_capt === '') { echo "Falta descripción."; exit; }

// Si no te mandaron lote/cad desde el modal, vuelve a derivarlos del código
if ($lote_capt === '' || $cad_capt === '') {
    $p2 = parse_gs1_like($codigo);
    $lote_der = trim($p2['lote'] ?? '');
    $cad_der  = trim($p2['caducidad'] ?? '');

    if ($lote_capt === '') $lote_capt = $lote_der;
    if ($cad_capt === '')  $cad_capt  = $cad_der;
}

// Normaliza caducidad vacía
if ($cad_capt === '') $cad_capt = '0000-00-00';

$ref_sql  = mysqli_real_escape_string($con, $ref_capt);
$desc_sql = mysqli_real_escape_string($con, $desc_capt);
$lote_sql = mysqli_real_escape_string($con, $lote_capt);
$cad_sql  = mysqli_real_escape_string($con, $cad_capt);

/**
 * Si hay OC: intenta completar el renglón pendiente (mismo ref, lote/cad vacíos)
 * Nota: como el modal siempre se abre, aquí también actualizamos cantidad/costo/exento.
 */
if ($id_oc > 0) {
    mysqli_query($con, "
        UPDATE tmp_recepcion
        SET
            descripcion_tmp = '$desc_sql',
            lote_tmp        = '$lote_sql',
            caducidad_tmp   = '$cad_sql',
            cantidad_tmp    = $cantidad,
            costo_tmp       = $costo,
            exento_iva_tmp  = $exento_iva,
            id_almacen_tmp  = $id_almacen
        WHERE session_id = '$session_id'
          AND id_oc = $id_oc
          AND referencia_tmp = '$ref_sql'
          AND (lote_tmp = '' OR caducidad_tmp = '' OR caducidad_tmp = '0000-00-00')
        ORDER BY id_tmp ASC
        LIMIT 1
    ");

    if (mysqli_affected_rows($con) > 0) {
        // Mantener exento_iva “maestro” si el producto existe
        mysqli_query($con, "UPDATE products SET exento_iva = $exento_iva WHERE referencia='$ref_sql' LIMIT 1");
        echo "OK";
        exit;
    }
}

// Si no actualizó (sin OC o no encontró pendiente), inserta nuevo renglón
mysqli_query($con, "
    INSERT INTO tmp_recepcion
    (referencia_tmp, descripcion_tmp, lote_tmp, caducidad_tmp, cantidad_tmp, costo_tmp, exento_iva_tmp, id_almacen_tmp, id_oc, session_id)
    VALUES
    ('$ref_sql', '$desc_sql', '$lote_sql', '$cad_sql', $cantidad, $costo, $exento_iva, $id_almacen, " . ($id_oc > 0 ? $id_oc : "NULL") . ", '$session_id')
");

if (mysqli_errno($con)) {
    echo "Error al guardar: " . mysqli_error($con);
    exit;
}

mysqli_query($con, "UPDATE products SET exento_iva = $exento_iva WHERE referencia='$ref_sql' LIMIT 1");

echo "OK";
