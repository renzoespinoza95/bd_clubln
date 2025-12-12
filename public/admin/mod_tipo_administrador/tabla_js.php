<script type="text/javascript">
// INICIO ON/OFF
// Nueva version moderna
$(document).ready( function(){


$(function() {
    $('.toggle_on_off').change(function() {
      //$('#console-event').html('Toggle: ' + $(this).prop('checked'))
      //var activado = $(this).prop('checked');
      var activado = $(this).prop('checked') ? 1 : 0;
      var tipo_administrador_id = $(this).attr('rel');
      console.log("tipo_administrador_id", tipo_administrador_id);
      console.log("activado", activado);      

      $.ajax({
              url: "<?php echo $apphost ?>/admin/isActivoTipoAdministrador/" + tipo_administrador_id + "/" + activado,
              dataType: "html",
              type: "get"      
            });
      
      })
})


}); 
// Fin ON/OFF


//+++++++++++++++++
//+   ELIMINAR    +
//+++++++++++++++++   

            
$('.link_eliminar_tipo_administrador').click(function(e){
  e.preventDefault();
  var tipo_administrador_id = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el tipo_administrador_id:' + tipo_administrador_id, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(config_blockui);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarTipoAdministrador" ?>',
                        dataType: "html",
                        data: 'tipo_administrador_id=' + tipo_administrador_id,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_tipo_administrador_id_' + tipo_administrador_id).fadeOut();                                                       
                        }
                    });
            //--
             }
    //--
    });
  
});  

$('#btn_eliminar_varios').click(function(e) {
    e.preventDefault();

    var checked = [];
    var tipo_administrador_id = "";
	
	//---
	
	var cantidad =  $("input[name='check_eliminar[]']:checked").length;
    
    if(cantidad == 0) {
        apprise("No ha seleccionado ningun item.");
        return false;
    }
	
	//---

    apprise('¿Desea eliminar los items seleccionados?', {
            'verify': true
        },
        function(r) {
            // --    
            if (r) {
                //--------------                

                $("input[name='check_eliminar[]']:checked").each(function() {
                    tipo_administrador_id = parseInt($(this).val());
                    checked.push(tipo_administrador_id);
					//agregar remove
                    $('#tr_tipo_administrador_id_' + tipo_administrador_id).fadeOut().remove();
                });

                $.blockUI(config_blockui);

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $apphost . "/admin/eliminarVariosTipoAdministrador" ?>',
                    dataType: "json",
                    data: {
                        info: checked
                    },
                    success: function(data) {
                        $.unblockUI();
                        mostrar_mensaje_superior("success", "Registros eliminados exitosamente");
                    }

                });


                //--------------                

            }
        }
    );



});
  

</script>  