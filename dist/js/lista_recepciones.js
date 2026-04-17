$(document).ready(function () {
  load_recepciones();
});

function load_recepciones() {
  $("#recepciones_ajax").load("../../ajax/buscar_recepciones.php?action=ajax");
}

function cancelar_recepcion(id_recepcion){
  if(!confirm("¿Deseas CANCELAR esta recepción?\nEsta acción no elimina inventario.")) return;

  $.ajax({
    type: "POST",
    url: "../../ajax/cancelar_recepcion.php",
    data: { id_recepcion: id_recepcion },
    success: function(r){
      if(r === "OK"){
        load_recepciones();
      }else{
        alert(r);
      }
    },
    error: function(xhr, status, error){
      alert("Error: " + error);
    }
  });
}