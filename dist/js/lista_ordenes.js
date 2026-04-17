$(document).ready(function(){
    load(1);
    console.log("Cargando lista de ordenes de compra");
});

function load(page){
    var q= $("#q").val();
    
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_ordenes.php?action=ajax&page='+page+'&q='+q,
         beforeSend: function(objeto){
          
      },
        success:function(data){
            $("#ordenes_compra_ajax").html(data).fadeIn('slow');
             console.log("Cargando lista de ordenes de compra");
            $('[data-toggle="tooltip"]').tooltip({html:true}); 
            
        }
    })
}

function eliminar_orden_compra(id_oc){
  if(!confirm("¿Desea Cancelar la orden de compra?")) return;

  $.ajax({
    type: "POST",
    url: "../../ajax/eliminar_orden_compra.php",
    data: { id_oc: id_oc },
    success: function(r){
      if(r === "OK"){
        // vuelve a cargar el listado como ya lo haces en este módulo
        // ejemplo:
        // load(1);
        location.reload();
      }else{
        alert(r);
      }
    },
    error: function(xhr, status, error){
      alert("Error: " + error);
    }
  });
}