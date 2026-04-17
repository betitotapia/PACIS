function cargar_tabla_tmp_recepcion(){
  $("#resultado_recepcion").load("../../ajax/tabla_tmp_recepcion.php");
}

$(document).ready(function(){
  cargar_tabla_tmp_recepcion();

  $("#id_proveedor").on("change", function(){
    cargar_ocs_proveedor();
  });

  $("#id_oc").on("change", function(){
    let id_oc = $(this).val();
    if (!id_oc) return;
    cargar_oc_en_tmp_recepcion(id_oc);
  });

  // Enter en GS1
  $("#codigo_gs1").on("keypress", function(e){
    if (e.which === 13) {
      e.preventDefault();
      agregar_por_codigo_barras();
    }
  });

  // Si vienes desde "Recibir" con ?id_oc=
  if (window.__OC_GET && parseInt(window.__OC_GET) > 0) {
    cargar_ocs_proveedor(window.__OC_GET);
  } else {
    if ($("#id_proveedor").val()) cargar_ocs_proveedor();
  }
});

function cargar_ocs_proveedor(id_oc_a_seleccionar=null){
  let id_proveedor = $("#id_proveedor").val();
  if (!id_proveedor) {
    $("#id_oc").html('<option value="">(Opcional) Seleccione OC</option>');
    return;
  }

  $.ajax({
    type: "GET",
    url: "../../ajax/obtener_ocs_proveedor.php",
    dataType: "json",
    data: { id_proveedor: id_proveedor },
    success: function(resp){
      let html = '<option value="">(Opcional) Seleccione OC</option>';
      if (resp && resp.length) {
        resp.forEach(function(oc){
          html += `<option value="${oc.id_oc}" data-id-almacen="${oc.id_almacen}">
                    ${oc.folio_oc} (${oc.estatus}) - Total: ${oc.total}
                  </option>`;
        });
      }
      $("#id_oc").html(html);

      if (id_oc_a_seleccionar) {
        $("#id_oc").val(String(id_oc_a_seleccionar)).trigger("change");
      }
    },
    error: function(){
      alert("No se pudieron cargar las órdenes de compra del proveedor.");
    }
  });
}

function cargar_oc_en_tmp_recepcion(id_oc){
  let id_almacen_hdr = $("#id_almacen_hdr").val();

  $.ajax({
    type: "POST",
    url: "../../ajax/cargar_oc_en_tmp_recepcion.php",
    data: {
      id_oc: id_oc,
      id_almacen: id_almacen_hdr
    },
    success: function(resp){
      if (resp.trim() === "OK") {
        let opt = $("#id_oc option:selected");
        let alm = opt.data("id-almacen");

        if (alm && !$("#id_almacen_hdr").val()) $("#id_almacen_hdr").val(String(alm));
        if (alm && !$("#almacen_tmp").val()) $("#almacen_tmp").val(String(alm));

        cargar_tabla_tmp_recepcion();
      } else {
        alert(resp);
      }
    },
    error: function(xhr, status, error){
      alert("Error al cargar OC: " + error);
    }
  });
}

/**
 * ===========================
 * AGREGAR POR CÓDIGO DE BARRAS (GS1)
 * ===========================
 * - Si hay OC seleccionada: completa lote/caducidad del renglón pendiente (UPDATE tmp).
 * - Si no hay OC: inserta renglón o pide referencia.
 */
function agregar_por_codigo_barras(){
  let codigo = $("#codigo_gs1").val().trim();
  if (!codigo) return;

  let id_oc = $("#id_oc").val() || "";
  let id_almacen = $("#id_almacen_hdr").val() || $("#almacen_tmp").val() || "";

  $.ajax({
    type: "POST",
    url: "../../ajax/agregar_item_recepcion_gs1.php",
    data: {
      codigo: codigo,
      id_oc: id_oc,
      id_almacen: id_almacen,
      modo: "PARSE" // 👈 IMPORTANTE
    },
    success: function(resp){
      const r = (resp || "").trim();

      // ✅ NUEVO: caso PARSE
      if (r.startsWith("NECESITA_DATOS")) {
        const p = r.split("|");
        const ref    = p[1] || "";
        const lote   = p[2] || "";
        const cad    = p[3] || "";
        const desc   = p[4] || "";
        const costo  = p[5] || "0";
        const exento = p[6] || "0";
        const cod    = p[7] || codigo;

        // 🔁 Soporta ambos nombres de inputs (por si tu modal usa IDs distintos)
        $("#np_codigo_gs1, #modalCodigo").val(cod);
        $("#np_referencia, #modalReferencia").val(ref);
        $("#np_lote, #modalLote").val(lote);
        $("#np_caducidad, #modalCaducidad").val(cad);

        $("#np_descripcion, #modalDescripcion").val(desc);
        $("#np_costo, #modalCosto").val(costo);
        $("#np_exento, #modalExentoIva").prop("checked", exento === "1");
        $("#np_cantidad, #modalCantidad").val(1);

        // Mostrar modal (Bootstrap 5)
        const modalEl = document.getElementById("modalNuevoProducto");
        if (!modalEl) {
          alert('No se encontró el modal con id="modalNuevoProducto" en la vista.');
          return;
        }
        if (window.bootstrap && bootstrap.Modal) {
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.show();
        } else if (window.jQuery && $("#modalNuevoProducto").modal) {
          $("#modalNuevoProducto").modal("show"); // fallback
        } else {
          alert("No se encontró Bootstrap Modal para mostrar el modal.");
        }

        return;
      }

      // (Opcional) si tu PHP todavía responde OK en algunos flujos
      if (r === "OK") {
        $("#codigo_gs1").val('');
        cargar_tabla_tmp_recepcion();
        return;
      }

      alert(r);
    },
    error: function(xhr, status, error){
      alert("Error al procesar código: " + error);
    }
  });
}
function guardar_nuevo_producto_desde_gs1() {

  // IDs EXACTOS de tu modal (nueva.php)
  const codigo     = ($("#np_codigo_gs1").val() || "").trim();
  const referencia = ($("#np_referencia").val() || "").trim();
  const descripcion= ($("#np_descripcion").val() || "").trim();
  const lote       = ($("#np_lote").val() || "").trim();
  const caducidad  = ($("#np_caducidad").val() || "").trim();
  const cantidad   = ($("#np_cantidad").val() || "1").trim();
  const costo      = ($("#np_costo").val() || "0").trim();
  const id_almacen = ($("#np_id_almacen").val() || "").trim();
  const id_oc      = ($("#id_oc").val() || "").trim();

  // Exento IVA: tu modal aún NO tiene checkbox.
  // Si lo agregas con id="np_exento", esto lo tomará; si no existe, manda 0.
  const exento_iva = ($("#np_exento").length && $("#np_exento").is(":checked")) ? 1 : 0;

  if (!codigo) { alert("Falta el código."); return; }
  if (!referencia) { alert("Captura referencia."); return; }
  if (!descripcion) { alert("Captura descripción."); return; }
  if (!id_almacen) { alert("Selecciona almacén destino."); return; }
  if (!cantidad || parseFloat(cantidad) <= 0) { alert("Cantidad inválida."); return; }
  if (costo === "" || isNaN(parseFloat(costo)) || parseFloat(costo) < 0) { alert("Costo inválido."); return; }

  $.ajax({
    type: "POST",
    // Desde dist/pages/recepciones/nueva.php la ruta correcta es ../../ajax/...
    url: "../../ajax/agregar_item_recepcion_gs1.php",
    data: {
      modo: "GUARDAR",
      codigo: codigo,
      referencia: referencia,
      descripcion: descripcion,
      lote: lote,
      caducidad: caducidad,
      cantidad: cantidad,
      costo: costo,
      exento_iva: exento_iva,
      id_almacen: id_almacen,
      id_oc: id_oc
    },
    success: function(resp){
      const r = (resp || "").trim();

      if (r === "OK") {

        // Cerrar modal (Bootstrap 5)
        const modalEl = document.getElementById("modalNuevoProducto");
        if (modalEl && window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else if (window.jQuery && $("#modalNuevoProducto").modal) {
          $("#modalNuevoProducto").modal("hide"); // fallback
        }

        // Limpiar para siguiente escaneo
        $("#codigo_gs1").val("");
        $("#np_codigo_gs1").val("");
        $("#np_referencia").val("");
        $("#np_descripcion").val("");
        $("#np_lote").val("");
        $("#np_caducidad").val("");
        $("#np_cantidad").val(1);
        $("#np_costo").val(0);
        if ($("#np_exento").length) $("#np_exento").prop("checked", false);

        // Refrescar tabla temporal
        cargar_tabla_tmp_recepcion();
        return;
      }

      alert(r);
    },
    error: function(xhr, status, error){
      alert("Error al guardar: " + (xhr.responseText || error));
    }
  });
}

/* ======== Mantienes tu alta manual + guardar/eliminar ======== */

function agregar_item_recepcion(){
  var datos = {
    referencia:  $("#ref_tmp").val(),
    descripcion: $("#desc_tmp").val(),
    lote:        $("#lote_tmp").val(),
    caducidad:   $("#cad_tmp").val(),
    cantidad:    $("#cant_tmp").val(),
    costo:       $("#costo_tmp").val(),
    id_almacen:  $("#almacen_tmp").val()
  };

  $.ajax({
    type: "POST",
    url: "../../ajax/agregar_item_recepcion.php",
    data: datos,
    success: function(respuesta){
      if (respuesta.trim() === "OK") {
        $("#ref_tmp,#desc_tmp,#lote_tmp,#cad_tmp,#costo_tmp").val('');
        $("#cant_tmp").val(1);
        cargar_tabla_tmp_recepcion();
      } else {
        alert(respuesta);
      }
    }
  });
}

function eliminar_item_recepcion(id_tmp){
  if(!confirm("¿Eliminar este renglón?")) return;

  $.ajax({
    type: "GET",
    url: "../../ajax/eliminar_item_recepcion.php",
    data: { id_tmp: id_tmp },
    success: function(respuesta){
      if (respuesta.trim() === "OK") cargar_tabla_tmp_recepcion();
      else alert(respuesta);
    }
  });
}

function guardar_recepcion(){
  $.ajax({
    type: "POST",
    url: "../../ajax/guardar_recepcion.php",
    data: $("#form_recepcion").serialize(),
    success: function(respuesta){
      var partes = respuesta.split("|");
      if (partes[0] === "OK") {
        var id_recepcion = partes[1];
        var folio = partes[2];

        cargar_tabla_tmp_recepcion();
        VentanaCentrada('../../pdf/recepcion_pdf.php?id_recepcion=' + id_recepcion,
          'Recepcion_' + folio,'','800','600','true');
      } else {
        alert(respuesta);
      }
    }
  });
}
function actualizar_item_recepcion(id_tmp){
  var datos = {
    id_tmp: id_tmp,
    descripcion: $("#desc_"+id_tmp).val(),
    lote: $("#lote_"+id_tmp).val(),
    caducidad: $("#cad_"+id_tmp).val(),
    cantidad: $("#cant_"+id_tmp).val(),
    costo: $("#costo_"+id_tmp).val()
  };

  $.ajax({
    type: "POST",
    url: "../../ajax/actualizar_item_recepcion.php",
    data: datos,
    success: function(r){
      if(r === "OK"){
        // recarga tabla para reflejar (opcional)
        cargar_tabla_tmp_recepcion();
      }else{
        alert(r);
      }
    },
    error: function(xhr, status, error){
      alert("Error AJAX: " + error);
    }
  });
}

