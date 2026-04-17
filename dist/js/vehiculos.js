$(document).ready(function(){
    load(1);
    
});

function load(page){
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_vehiculos.php?action=ajax&page='+page+'&q='+q,
         beforeSend: function(objeto){
         $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
      },
        success:function(data){
            $(".outer_div").html(data).fadeIn('slow');
            $('#loader').html('');
            $('[data-toggle="tooltip"]').tooltip({html:true}); 
            
        }
    })
}


    function eliminar (id)
{
    var q= $("#q").val();
if (confirm("Realmente deseas cancelar esta remisión")){	
$.ajax({
type: "GET",
url: "./ajax/buscar_facturas.php",
data: "id="+id,"q":q,
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
function descargar(id_factura, numero_factura){
    VentanaCentrada('./pdf/documentos/ver_factura_excel.php?id_factura='+id_factura+'&numero_factura='+numero_factura);
}
function imprimir_factura(id_factura, numero_factura){
    VentanaCentrada('./pdf/documentos/ver_factura.php?id_factura='+id_factura+'&numero_factura='+numero_factura);
}
function ver_factura(id_factura, numero_factura){
    VentanaCentrada('./pdf/documentos/ver_factura2.php?id_factura='+id_factura+'&numero_factura='+numero_factura);
}



function notificaciones(tipo){
// Push.create('SUMED', {
// 	body: tipo,
// 	icon: 'icon.png',
// 	timeout: 8000,               // Timeout before notification closes automatically.
// 	vibrate: [100, 100, 100],    // An array of vibration pulses for mobile devices.
// 	onClick: function() {
// 		// Callback for when the notification is clicked. 
// 		console.log(this);
// 	}  
// });
}