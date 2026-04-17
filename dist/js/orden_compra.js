
function cargar_tmp_oc() {
  $("#resultado_tmp_oc").load("../../ajax/tabla_tmp_oc.php");
}

$(document).ready(function () {
  cargar_tmp_oc();
});

function agregar_item_oc() {
  var datos = {
    referencia: $("#ref_tmp_oc").val(),
    descripcion: $("#desc_tmp_oc").val(),
    cantidad: $("#cant_tmp_oc").val(),
    costo: $("#costo_tmp_oc").val()
  };

  $.ajax({
    type: "POST",
    url: "../../ajax/agregar_item_oc.php",
    data: datos,
    success: function (resp) {
      if (resp.trim() === "OK") {
        $("#ref_tmp_oc").val("");
        $("#desc_tmp_oc").val("");
        $("#cant_tmp_oc").val(1);
        $("#costo_tmp_oc").val(0);
        cargar_tmp_oc();
      } else {
        alert(resp);
      }
    }
  });
}

function eliminar_item_oc(id_tmp_oc) {
  if (!confirm("¿Eliminar esta partida?")) return;

  $.ajax({
    type: "POST",
    url: "../../ajax/eliminar_item_oc.php",
    data: { id_tmp_oc: id_tmp_oc },
    success: function (resp) {
      if (resp.trim() === "OK") {
        cargar_tmp_oc();
      } else {
        alert(resp);
      }
    }
  });
}

function guardar_oc() {
  var form = $("#form_oc");

  $.ajax({
    type: "POST",
    url: "../../ajax/guardar_oc.php",
    data: form.serialize(),
    success: function (resp) {
      var partes = resp.split("|");
      if (partes[0] === "OK") {
        var id_oc = partes[1];
        var folio = partes[2];
        alert("OC guardada. Folio: " + folio);
        window.location.href = "index.php";
      } else {
        alert(resp);
      }
    }
  });
}
