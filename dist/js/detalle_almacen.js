$(document).ready(function(){
    load(1);
    
});

function load(page){
    var id_almacen = document.getElementById('id_almacen').value;
    console.log("Cargando almacén con ID: " + id_almacen);
    var q= $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_almacenes_detalle.php?action=ajax&id_almacen='+id_almacen,
         beforeSend: function(objeto){
          spinner.display='block';
      },
        success:function(data){
            console.log("Datos cargados correctamente");
            $(".outer_div").html(data).fadeIn('slow');
             
            spinner.style.display='none';
            $('[data-toggle="tooltip"]').tooltip({html:true}); 
            
        }
    })
}

	

  

