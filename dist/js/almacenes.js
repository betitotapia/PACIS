$(document).ready(function(){
    load(1);
    
});

function load(page){
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_almacenes.php?action=ajax&page='+page+'&q='+q,
         beforeSend: function(objeto){
          spinner.display='block';
      },
        success:function(data){
            $(".outer_div").html(data).fadeIn('slow');
             
            spinner.style.display='none';
            $('[data-toggle="tooltip"]').tooltip({html:true}); 
            
        }
    })
}

    function eliminar (id)
{
  
if (confirm("Realmente deseas eliminar este almacen")){	
$.ajax({
type: "GET",
url: "",
data: "id="+id,
 beforeSend: function(objeto){
    $("#resultados").html("Mensaje: Cargando...");
  },
success: function(datos){
$("#resultados").html(datos);
load(1);
    
}
    });
}
}

