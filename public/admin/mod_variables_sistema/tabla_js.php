<script type="text/javascript">

//+++++++++++++++++
//+   ELIMINAR    +
//+++++++++++++++++   
            
$('.link_eliminar_variables_sistema').click(function(e){
  e.preventDefault();
  var nombre_variable = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el nombre_variable:' + nombre_variable, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(config_blockui);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarVariablesSistema" ?>',
                        dataType: "html",
                        data: 'nombre_variable=' + nombre_variable,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_nombre_variable_' + nombre_variable).fadeOut();                                                       
                        }
                    });
            //--
             }
    //--
    });
  
});  

$(document).ready(function(){
    $('#tbl_variables_sistema').DataTable({
        // opciones personalizadas aquí (opcional)
        paging: true,
        searching: true,        
    });
});




</script>  