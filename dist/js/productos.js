$(document).ready(function(){
    load(1);
});

function load(page){
    var q= $("#q").val();
    
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_productos.php?action=ajax&page='+page+'&q='+q,
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
    var q= $("#q").val();
//if (confirm("Realmente deseas eliminar el producto")){	
if (Swal.fire({
    title: 'Eliminar producto',
    text: "Realmente deseas eliminar el producto",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
    }).then((result) => {
    if (result.isConfirmed) {   
        $.ajax({
            type: "GET",
            url: "../../ajax/buscar_productos.php",
            data: "id="+id,"q":q,
            beforeSend: function(objeto){
                $("#resultados").html("Mensaje: Cargando...");
              },
            success: function(datos){
                $("#resultados").html(datos);
                load(1);
            }
                }); //
            }
    })) ;                        
         }


