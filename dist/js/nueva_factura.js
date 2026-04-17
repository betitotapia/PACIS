$(document).ready(function(){
    load(1);
    $( "#resultados" ).load( "ajax/clonar_facturacion.php" );
});

function load(page){
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'./ajax/productos_factura.php?action=ajax&page='+page+'&q='+q,
         beforeSend: function(objeto){
         $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
      },
        success:function(data){
            $(".outer_div").html(data).fadeIn('slow');
            $('#loader').html('');
            
        }
    })
}

function agregar (id)
{
    var precio_venta=document.getElementById('precio_venta_'+id).value;
    var cantidad=document.getElementById('cantidad_'+id).value;
    var lote=document.getElementById('lote_'+id).value;
    var caducidad=document.getElementById('caducidad_'+id).value;
    var referencia=document.getElementById('referencia_'+id).value;
    var almacen=document.getElementById('almacen_'+id).value;

    
    //Inicia validacion
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
    //Fin validacion
    //newid= id.split("/");
    //res=newid[0];
    //indice = id.indexOf("/");
    //let extraida = id.substring(0,indice);
    
    $.ajax({
type: "POST",
url: "./ajax/clonar_facturacion.php",
data: "id="+id+"&precio_venta="+precio_venta+"&cantidad="+cantidad+"&lote="+lote+"&caducidad="+caducidad+"&referencia="+referencia+"&almacen="+almacen,
 beforeSend: function(objeto){
    $("#resultados").html("Mensaje: Cargando...");
  },
success: function(datos){
$("#resultados").html(datos);
}
    });
}


    function eliminar (id)
{
    
    $.ajax({
type: "GET",
url: "./ajax/clonar_facturacion.php",
data: "id="+id,
 beforeSend: function(objeto){
    $("#resultados").html("Mensaje: Cargando...");
  },
success: function(datos){
$("#resultados").html(datos);
}
    });

}


$("#datos_factura").submit(function(){
  var id_cliente = $("#id_cliente").val();
  var id_vendedor = $("#id_vendedor").val();
  var letra_ventas = $("#letra_ventas").val();
  var compra = $("#compra").val();
  var cotizacion = $("#cotizacion").val();
  var doctor = $("#doctor").val();
  var paciente = $("#paciente").val();
  var material = $("#material").val();
  var pago = $("#pago").val();
  var d_factura = $("#d_factura").val();
  var observaciones = $("#observaciones").val();
  var fecha = $("#fecha").val();

  if (id_cliente==""){
    alert("Debes seleccionar un cliente");
    $("#nombre_cliente").focus();
    return false;
  }
 VentanaCentrada('./pdf/documentos/factura_pdf.php?id_cliente='+id_cliente+'&id_vendedor='+id_vendedor+'&letra_ventas='+letra_ventas+'&compra='+compra+'&cotizacion='+cotizacion+'&fecha='+fecha+'&doctor='+doctor+'&paciente='+paciente+'&material='+material+'&pago='+pago+'&d_factura='+d_factura+'&observaciones='+observaciones);
 });

$( "#guardar_cliente" ).submit(function( event ) {
  $('#guardar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
   $.ajax({
      type: "POST",
      url: "ajax/nuevo_cliente.php",
      data: parametros,
       beforeSend: function(objeto){
        $("#resultados_ajax").html("Mensaje: Cargando...");
        },
      success: function(datos){
      $("#resultados_ajax").html(datos);
      $('#guardar_datos').attr("disabled", false);
      load(1);
      }
  });
  event.preventDefault();
})


$( "#guardar_cliente" ).submit(function( event ) {
  $('#guardar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
     $.ajax({
            type: "POST",
            url: "ajax/nuevo_cliente.php",
            data: parametros,
             beforeSend: function(objeto){
                $("#resultados_ajax").html("Mensaje: Cargando...");
              },
            success: function(datos){
            $("#resultados_ajax").html(datos);
            $('#guardar_datos').attr("disabled", false);
            load(1);
          }
    });
  event.preventDefault();
})

$( "#guardar_producto" ).submit(function( event ) {
  $('#guardar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
     $.ajax({
            type: "POST",
            url: "ajax/nuevo_producto.php",
            data: parametros,
             beforeSend: function(objeto){
                $("#resultados_ajax_productos").html("Mensaje: Cargando...");
              },
            success: function(datos){
            $("#resultados_ajax_productos").html(datos);
            $('#guardar_datos').attr("disabled", false);
            load(1);
          }
    });
  event.preventDefault();
})

function edit_item(id_detalle,item){
  
if (item == 'almacen_'+id_detalle){
  document.getElementById('almacen_'+id_detalle).setAttribute('disabled',false);
}else if(item == 'lote_'+id_detalle){
  document.getElementById('lote_'+id_detalle).removeAttribute('readonly');
}else if(item == 'caducidad_'+id_detalle){
  document.getElementById('caducidad_'+id_detalle).removeAttribute('readonly');
}else if(item == 'precio_'+id_detalle){
  document.getElementById('precio_'+id_detalle).removeAttribute('readonly');
}
else{
  document.getElementById('cantidad_'+id_detalle).removeAttribute('readonly');
  console.log(item);
}
}


function save_item(id_detalle,select){
  
switch (select){

case 0:
  document.getElementById('almacen_'+id_detalle).setAttribute('disabled',true);
  var item = document.getElementById('almacen_'+id_detalle).value;
  var tipo = 'almacen';
  console.log('estamos aqui'+select+' item '+item+' tipo '+tipo+' id_detalle '+id_detalle);
  
break;

case 1:

  document.getElementById('lote_'+id_detalle).setAttribute('readonly',true);
  var item = document.getElementById('lote_'+id_detalle).value;
  var tipo = 'lote';
  console.log('estamos aqui'+select+' item '+item+' tipo '+tipo+' id_detalle '+id_detalle);
break;

case 2:

document.getElementById('cantidad_'+id_detalle).setAttribute('readonly',true);
var item = document.getElementById('cantidad_'+id_detalle).value;
var tipo = 'cantidad';
console.log('estamos aqui'+select+' item '+item+' tipo '+tipo+' id_detalle '+id_detalle);

break;

case 3:

document.getElementById('precio_'+id_detalle).setAttribute('readonly',true);
var item = document.getElementById('precio_'+id_detalle).value;
var tipo = 'precio';
console.log('estamos aqui '+item);
break;

case 4:

document.getElementById('caducidad_'+id_detalle).setAttribute('readonly',true);
var item = document.getElementById('caducidad_'+id_detalle).value;
var tipo = 'caducidad';
var tipo_dato = typeof item;
console.log('estamos aqui '+item+' tipo de dato '+ tipo_dato);
break;

default:
  console.log('estas aki'+select);
  console.log(typeof select);
  break;

}

  $.ajax({
    type: "GET",
    url: "./ajax/clonar_facturacion.php",
    data: "id_detalle="+id_detalle+"&tipo="+tipo+"&item="+item,
     beforeSend: function(objeto){
        $("#resultados").html("Mensaje: Cargando...");
      },
    success: function(datos){
    $("#resultados").html(datos);
    }
        });
  
}