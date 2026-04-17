$(document).ready(function(){
    load();
    
});

function load(){
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_clientes.php?action=ajax&page=',
         beforeSend: function(objeto){
         
      },
        success:function(data){
            $(".outer_div").html(data).fadeIn('slow');
           
            
        }
    })
}

