
$(document).ready(function(){
    load(1);
    cargar_remision()
});
const numero_factura=document.getElementById('numero_factura').value;
function load(page){
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/productos_remision.php?action=ajax&page='+page+'&q='+q,
         beforeSend: function(objeto){
        // $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
      },
        success:function(data){
            $(".outer_div").html(data).fadeIn('slow');
            $('#loader').html('');
        }
    })
}
function cargar_remision(){

const numero_factura=document.getElementById('numero_factura').value;
const id_vendedor=document.getElementById('id_vendedor').value;
$('#resultados').load('../../ajax/agregar_facturacion.php',{
    numero_factura: numero_factura,
    id_vendedor: id_vendedor
});
}


function editar_item(id,item){
   
var element = document.getElementById(item);

 if (item == 'descripcion_'+id){
     element.removeAttribute('readonly');
     console.log('editando'+item);
 }else if (item == 'cantidad_'+id){
    element.removeAttribute('readonly');
     console.log('editando'+item);
     
 }

}
function guardar_item(id,select){
    

switch (select){


case 1:

  document.getElementById('cantidad_'+id).setAttribute('readonly',true);
  var item = document.getElementById('cantidad_'+id).value;
  var tipo = 'cantidad';
break;

case 2:
    document.getElementById('descripcion_'+id).setAttribute('readonly',true);
  var item = document.getElementById('descripcion_'+id).value;
  var tipo = 'descripcion';
  
break;
}

$.ajax({
    type: "GET",
    url: "../../ajax/editar_productos_ajax.php",
    data: "id="+id+"&tipo="+tipo+"&item="+item,
        beforeSend: function(objeto){
            $("#resultados").html("Mensaje: Cargando...");
        },
        success: function(datos){
        $("#resultados").html(datos);
        cargar_remision();
        }
        });


}

function agregar (id)
{
    
    //Inicia validacion
    var precio_venta=document.getElementById('precio_venta_'+id).value;
    var cantidad=document.getElementById('cantidad_'+id).value;
    var lote=document.getElementById('lote_'+id).value;
    var caducidad=document.getElementById('caducidad_'+id).value;
    var referencia=document.getElementById('referencia_'+id).value;
    var descripcion=document.getElementById('descripcion_'+id).value;
    var almacen=document.getElementById('almacen_'+id).value;
    var n_factura=document.getElementById('numero_factura').value;
    var vendedor=document.getElementById('id_vendedor').value;
    var existencias=document.getElementById('existencias_'+id).value;
    
    if (isNaN(cantidad))
    {
    alert('Esto no es un numero');
    document.getElementById('cantidad_'+id).focus();
    if(cantidad == null || cantidad== ""){
        alert('Por favor ingresa una cantidad');
    document.getElementById('cantidad_'+id).focus();
    }
    return false;
    }
    if(cantidad > existencias){
        alert('No hay existencias suficientes')
        return false;
    }

    if (isNaN(precio_venta)|| precio_venta ==null || precio_venta =="")
    {
    alert('Esto no es un numero');
    document.getElementById('precio_venta_'+id).focus();
    if(precio_venta==null || precio_venta==""){
        alert('Por favor ingresa un precio');
    document.getElementById('precio_'+id).focus();
    }
    return false;
    }
    if (lote == null || lote == "")
    {
    alert('Por favor ingresa el lote');
    document.getElementById('lote_'+id).focus();
    return false;
    }
   
    $.ajax({
type: "POST",
url: "../../ajax/agregar_facturacion.php",
data: "id="+id+"&precio_venta="+precio_venta+"&cantidad="+cantidad+"&lote="+lote+"&caducidad="+caducidad+"&referencia="+referencia+"&descripcion="+descripcion+"&almacen="+almacen+"&numero_factura="+n_factura+"&id_vendedor="+ vendedor,
 beforeSend: function(objeto){
    $("#resultados").html("Mensaje: Cargando...");

  },
success: function(datos){
$("#resultados").html(datos);
console.log(datos);
}
    });
}

function eliminar(id,producto,cantidad,event) {
    event.preventDefault(); // Evita que el enlace recargue la página
    console.log(cantidad);
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        $.ajax({
            url: '',
            type: 'GET',
            data: { id: id , producto: producto, cantidad: cantidad },
            success: function(response) {
                
                // Actualiza la vista después de eliminar el producto
                cargar_remision(); // Recarga la lista de productos
            }
        });
    }
}
