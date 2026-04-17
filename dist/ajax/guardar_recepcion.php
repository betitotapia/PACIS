<?php
/**
 * ============================================================
 * GUARDAR RECEPCIÓN - PRO + TRANSACCIONES + FOLIO PRO
 * ============================================================
 * Requiere (recomendado InnoDB):
 * - consecutivos(nombre PK, valor INT) con fila ('folio_recepcion', N)
 * - tmp_recepcion: id_tmp, id_oc, id_det_oc, referencia_tmp, descripcion_tmp, lote_tmp, caducidad_tmp,
 *                  cantidad_tmp, costo_tmp, id_almacen_tmp, session_id
 * - recepciones: incluye id_oc
 * - recepciones_detalle: incluye id_det_oc
 * - ordenes_compra_detalle: incluye id_det_oc, cantidad_solicitada, cantidad_recibida
 *
 * Respuesta: OK|id_recepcion|folio
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

// Convertir errores de MySQLi en excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    /* ---------------------------
     * 1) VALIDACIONES
     * --------------------------- */
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Sesión inválida.");
    }
    $id_usuario = (int)$_SESSION['user_id'];

    $fecha_rec      = isset($_POST['fecha_recepcion']) ? trim($_POST['fecha_recepcion']) : '';
    $id_proveedor   = isset($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : 0;
    $id_almacen_hdr = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen'] : 0;
    $observaciones  = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $id_oc          = isset($_POST['id_oc']) ? (int)$_POST['id_oc'] : 0;

    if ($fecha_rec === '') throw new Exception("Debe capturar la fecha de la recepción.");
    if ($id_proveedor <= 0) throw new Exception("Debe seleccionar un proveedor válido.");
    if ($id_almacen_hdr <= 0) throw new Exception("Debe seleccionar un almacén válido.");

    // ─────────────────────────────────────
// VALIDAR QUE NO HAYA RENGLONES SIN LOTE O CADUCIDAD
// ─────────────────────────────────────
        $chk = mysqli_query($con, "
        SELECT COUNT(*) AS faltantes
        FROM tmp_recepcion
        WHERE session_id = '$session_id'
            AND (
            lote_tmp = ''
            OR lote_tmp IS NULL
            OR caducidad_tmp IS NULL
            OR caducidad_tmp = '0000-00-00'
            )
        ");

        if (!$chk) {
            echo "Error al validar datos de los productos: " . mysqli_error($con);
            exit;
        }

        $rw = mysqli_fetch_assoc($chk);
        if ((int)$rw['faltantes'] > 0) {
            echo "Hay renglones sin LOTE o CADUCIDAD. Completa los datos antes de guardar la recepción.";
            exit;
        }

    $fecha_rec     = mysqli_real_escape_string($con, $fecha_rec);
    $observaciones = mysqli_real_escape_string($con, $observaciones);

    /* ---------------------------
     * 2) VALIDAR TMP
     * --------------------------- */
    $res_tmp = mysqli_query($con, "SELECT * FROM tmp_recepcion WHERE session_id = '$session_id'");
    if (mysqli_num_rows($res_tmp) == 0) {
        throw new Exception("No hay productos en la recepción.");
    }

    /* =========================================================
     * INICIO TRANSACCIÓN
     * ========================================================= */
    mysqli_begin_transaction($con);

    /* ---------------------------
     * 3) FOLIO PRO (consecutivos + LAST_INSERT_ID)
     * --------------------------- */
    mysqli_query($con, "
        UPDATE consecutivos
        SET valor = LAST_INSERT_ID(valor + 1)
        WHERE nombre = 'folio_recepcion'
    ");

    // ✅ Validación crítica: si no existe el consecutivo, abortar
    if (mysqli_affected_rows($con) <= 0) {
        throw new Exception("No existe consecutivo 'folio_recepcion' en la tabla consecutivos.");
    }

    $res_f = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
    $rw_f  = mysqli_fetch_assoc($res_f);
    $folio = (int)$rw_f['folio'];

    if ($folio <= 0) {
        throw new Exception("No se pudo generar folio válido.");
    }

    /* ---------------------------
     * 4) INSERT ENCABEZADO (incluye id_oc)
     * --------------------------- */
    $id_oc_sql = ($id_oc > 0) ? $id_oc : "NULL";

    $sql_enc = "
        INSERT INTO recepciones
        (folio, fecha_recepcion, id_usuario, id_proveedor, id_almacen, observaciones, id_oc)
        VALUES
        ($folio, '$fecha_rec', $id_usuario, $id_proveedor, $id_almacen_hdr, '$observaciones', $id_oc_sql)
    ";
    mysqli_query($con, $sql_enc);
    $id_recepcion = (int)mysqli_insert_id($con);

    /* ---------------------------
     * 5) RECORRER TMP: DETALLE + INVENTARIO + OC
     * --------------------------- */
    mysqli_data_seek($res_tmp, 0);

    while ($row = mysqli_fetch_assoc($res_tmp)) {

        $referencia  = mysqli_real_escape_string($con, $row['referencia_tmp']);
        $descripcion = mysqli_real_escape_string($con, $row['descripcion_tmp']);
        $lote        = mysqli_real_escape_string($con, $row['lote_tmp']);
        $caducidad   = mysqli_real_escape_string($con, $row['caducidad_tmp']);

        $cantidad    = (float)$row['cantidad_tmp'];
        $costo_unit  = (float)$row['costo_tmp'];
        $id_almacen  = (int)$row['id_almacen_tmp'];
        $exento_iva  = (isset($row['exento_iva']) && $row['exento_iva']) ? 1 : 0;

        $id_det_oc   = !empty($row['id_det_oc']) ? (int)$row['id_det_oc'] : 0;
        $id_det_oc_sql = ($id_det_oc > 0) ? $id_det_oc : "NULL";

        if ($cantidad <= 0) {
            throw new Exception("Cantidad inválida detectada en temporales.");
        }
        if ($id_almacen <= 0) {
            // fallback: usar el almacén del encabezado si el tmp viene vacío
            $id_almacen = $id_almacen_hdr;
        }

        // 5.1 Insert detalle de recepción (PRO: incluye id_det_oc)
        $sql_det = " INSERT INTO recepciones_detalle (id_recepcion, id_det_oc, referencia, descripcion, lote, caducidad, cantidad, costo_unitario, id_almacen, exento_iva)
            VALUES ($id_recepcion, $id_det_oc_sql, '$referencia', '$descripcion', '$lote', '$caducidad', $cantidad, $costo_unit, $id_almacen, $exento_iva)
        ";
        mysqli_query($con, $sql_det);

        // 5.2 Inventario: lock fila si existe
        $q_prod = mysqli_query(
            $con,
            "SELECT id_producto
             FROM products
             WHERE referencia = '$referencia'
               AND lote = '$lote'
               AND id_almacen = $id_almacen
             LIMIT 1
             FOR UPDATE"
        );

        if (mysqli_num_rows($q_prod) > 0) {
            $rw_prod = mysqli_fetch_assoc($q_prod);
            $id_producto = (int)$rw_prod['id_producto'];

            mysqli_query(
                $con,
                "UPDATE products
                 SET existencias = existencias + $cantidad,
                     ultima_modificacion = NOW()
                 WHERE id_producto = $id_producto"
            );
        } else {
            // fallback: crear producto si no existía
            mysqli_query(
                $con,
                "INSERT INTO products
                 (barcode, referencia, descripcion, existencias, lote, caducidad, costo, precio_producto, id_almacen, estatus, ultima_modificacion)
                 VALUES
                 ('', '$referencia', '$descripcion', $cantidad, '$lote', '$caducidad', $costo_unit, 0, $id_almacen, 1, NOW())"
            );
        }

        // 5.3 OC PRO: actualizar recibido por renglón (id_det_oc)
        if ($id_oc > 0 && $id_det_oc > 0) {

            $q_pend = mysqli_query(
                $con,
                "SELECT cantidad_solicitada, cantidad_recibida
                 FROM ordenes_compra_detalle
                 WHERE id_det_oc = $id_det_oc
                   AND id_oc = $id_oc
                 LIMIT 1
                 FOR UPDATE"
            );

            if (mysqli_num_rows($q_pend) == 0) {
                throw new Exception("Partida de OC no encontrada (id_det_oc=$id_det_oc) o no pertenece a la OC seleccionada.");
            }

            $rw = mysqli_fetch_assoc($q_pend);
            $pendiente = (float)$rw['cantidad_solicitada'] - (float)$rw['cantidad_recibida'];

            // No exceder el pendiente
            $cant_rec = $cantidad;
            if ($pendiente > 0 && $cant_rec > $pendiente) {
                $cant_rec = $pendiente;
            }

            if ($cant_rec > 0) {
                mysqli_query(
                    $con,
                    "UPDATE ordenes_compra_detalle
                     SET cantidad_recibida = cantidad_recibida + $cant_rec
                     WHERE id_det_oc = $id_det_oc
                       AND id_oc = $id_oc"
                );
            }
        }
    }

    /* ---------------------------
     * 6) ACTUALIZAR ESTATUS OC (una vez)
     * --------------------------- */
    if ($id_oc > 0) {
        $q = mysqli_query(
            $con,
            "SELECT SUM(cantidad_solicitada - cantidad_recibida) AS pendiente
             FROM ordenes_compra_detalle
             WHERE id_oc = $id_oc"
        );

        $pend = (float)(mysqli_fetch_assoc($q)['pendiente'] ?? 0);
        $nuevo_estatus = ($pend <= 0) ? 'CERRADA' : 'PARCIAL';

        mysqli_query(
            $con,
            "UPDATE ordenes_compra
             SET estatus = '$nuevo_estatus'
             WHERE id_oc = $id_oc"
        );
    }

    /* ---------------------------
     * 7) LIMPIAR TMP
     * --------------------------- */
    mysqli_query($con, "DELETE FROM tmp_recepcion WHERE session_id = '$session_id'");

    /* =========================================================
     * COMMIT
     * ========================================================= */
    mysqli_commit($con);

    echo "OK|$id_recepcion|$folio";
    exit;

} catch (Exception $e) {
    // rollback seguro
    try { mysqli_rollback($con); } catch (Throwable $t) { /* ignore */ }

    echo "Error: " . $e->getMessage();
    exit;
}
