<script type="text/javascript">
// INICIO ON/OFF
// Nueva version moderna
$(document).ready( function(){


$(function() {
    $('.toggle_on_off').change(function() {
      //$('#console-event').html('Toggle: ' + $(this).prop('checked'))
      //var activado = $(this).prop('checked');
      var activado = $(this).prop('checked') ? 1 : 0;
      var administrador_id = $(this).attr('rel');
      console.log("administrador_id", administrador_id);
      console.log("activado", activado);      

      $.ajax({
              url: "<?php echo $apphost ?>/admin/isActivoAdministradortbl/" + administrador_id + "/" + activado,
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

            
$('.link_eliminar_administradortbl').click(function(e){
  e.preventDefault();
  var administrador_id = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el administrador_id:' + administrador_id, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(config_blockui);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarAdministradortbl" ?>',
                        dataType: "html",
                        data: 'administrador_id=' + administrador_id,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_administrador_id_' + administrador_id).fadeOut();                                                       
                        }
                    });
            //--
             }
    //--
    });
  
});    

</script>  