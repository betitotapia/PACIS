function cargar_tabla_tmp_traspaso() {
    $("#resultado_traspaso").load("../../ajax/tabla_tmp_traspaso.php");
}

$(document).ready(function () {
    cargar_tabla_tmp_traspaso();

    // Enter en búsqueda lanza la búsqueda
    $("#ref_buscar").on("keypress", function (e) {
        if (e.which === 13) { e.preventDefault(); buscar_producto_almacen(); }
    });

    // Ocultar panel si se limpia la referencia
    $("#ref_buscar").on("input", function () {
        if ($(this).val().trim() === '') {
            $("#panel_busqueda").hide();
        }
    });
});

/* ─────────────────────────────────────────────
   BUSCAR productos del almacén origen
───────────────────────────────────────────── */
function buscar_producto_almacen() {
    var referencia       = $("#ref_buscar").val().trim();
    var id_almacen_origen = $("#id_almacen_origen").val();

    if (!referencia) {
        alert("Ingresa una referencia para buscar.");
        return;
    }
    if (!id_almacen_origen) {
        alert("Selecciona el almacén de origen primero.");
        return;
    }

    $("#tbody_busqueda").html(
        '<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Buscando...</td></tr>'
    );
    $("#panel_busqueda").show();

    $.ajax({
        type: "POST",
        url: "../../ajax/buscar_producto_almacen_traspaso.php",
        data: { referencia: referencia, id_almacen_origen: id_almacen_origen },
        success: function (html) {
            $("#tbody_busqueda").html(html);
        },
        error: function () {
            $("#tbody_busqueda").html(
                '<tr><td colspan="7" class="text-center text-danger">Error al realizar la búsqueda.</td></tr>'
            );
        }
    });
}

/* ─────────────────────────────────────────────
   AGREGAR un lote desde los resultados de búsqueda
   referencia: string, lote: string, id_producto: int (usado para leer el input de cantidad)
───────────────────────────────────────────── */
function agregar_lote_traspaso(referencia, lote, id_producto) {
    var cantidad           = parseFloat($("#cant_lote_" + id_producto).val()) || 0;
    var id_almacen_origen  = $("#id_almacen_origen").val();
    var id_almacen_destino = $("#id_almacen_destino").val();

    if (cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero.");
        return;
    }
    if (!id_almacen_destino) {
        alert("Selecciona el almacén de destino primero.");
        return;
    }
    if (id_almacen_origen === id_almacen_destino) {
        alert("El almacén de origen y destino no pueden ser el mismo.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "../../ajax/agregar_item_traspaso.php",
        data: {
            referencia:         referencia,
            lote:               lote,
            cantidad:           cantidad,
            id_almacen_origen:  id_almacen_origen,
            id_almacen_destino: id_almacen_destino
        },
        success: function (resp) {
            if (resp.trim() === "OK") {
                cargar_tabla_tmp_traspaso();
                // Refrescar resultados de búsqueda para mostrar stock actualizado
                buscar_producto_almacen();
            } else {
                alert(resp);
            }
        },
        error: function (xhr, status, error) {
            alert("Error al agregar: " + error);
        }
    });
}

/* ─────────────────────────────────────────────
   TABLA TEMPORAL — editar / eliminar
───────────────────────────────────────────── */
function eliminar_item_traspaso(id_tmp) {
    if (!confirm("¿Eliminar este renglón?")) return;

    $.ajax({
        type: "GET",
        url: "../../ajax/eliminar_item_traspaso.php",
        data: { id_tmp: id_tmp },
        success: function (resp) {
            if (resp.trim() === "OK") cargar_tabla_tmp_traspaso();
            else alert(resp);
        }
    });
}

function actualizar_item_traspaso(id_tmp) {
    var cantidad = $("#tcant_" + id_tmp).val();

    $.ajax({
        type: "POST",
        url: "../../ajax/actualizar_item_traspaso.php",
        data: { id_tmp: id_tmp, cantidad: cantidad },
        success: function (r) {
            if (r.trim() === "OK") cargar_tabla_tmp_traspaso();
            else alert(r);
        },
        error: function (xhr, status, error) {
            alert("Error AJAX: " + error);
        }
    });
}

/* ─────────────────────────────────────────────
   GUARDAR TRASPASO
───────────────────────────────────────────── */
function guardar_traspaso() {
    var id_origen  = $("#id_almacen_origen").val();
    var id_destino = $("#id_almacen_destino").val();

    if (!id_origen)  { alert("Selecciona el almacén de origen."); return; }
    if (!id_destino) { alert("Selecciona el almacén de destino."); return; }
    if (id_origen === id_destino) {
        alert("El almacén de origen y destino no pueden ser el mismo.");
        return;
    }

    if (!confirm("¿Confirmas realizar el traspaso? Esta acción moverá el inventario entre almacenes.")) return;

    $.ajax({
        type: "POST",
        url: "../../ajax/guardar_traspaso.php",
        data: $("#form_traspaso").serialize(),
        success: function (resp) {
            var partes = resp.split("|");
            if (partes[0] === "OK") {
                var id_traspaso = partes[1];
                var folio       = partes[2];

                cargar_tabla_tmp_traspaso();
                VentanaCentrada(
                    '../../pdf/print_traspaso.php?id_traspaso=' + id_traspaso,
                    'Traspaso_' + folio, '', '860', '650', 'true'
                );
                // Limpiar formulario
                $("#form_traspaso")[0].reset();
                $("#id_almacen_origen, #id_almacen_destino").val('');
                $("#panel_busqueda").hide();
                $("#ref_buscar").val('');
            } else {
                alert(resp);
            }
        },
        error: function (xhr, status, error) {
            alert("Error al guardar el traspaso: " + error);
        }
    });
}
