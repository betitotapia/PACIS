$(document).ready(function(){
    load();
    
});

function load(){
    $("#loader").fadeIn('slow');
    $.ajax({
        url:'../../ajax/buscar_usuarios.php?action=ajax&page=',
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


