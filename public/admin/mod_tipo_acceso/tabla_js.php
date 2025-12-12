<script type="text/javascript">
<?php
if($total_registros > 0) {
$lista = tipo_acceso::lista_tipo_acceso_edu(
    $paginacion->limit_start, 
    $paginacion->limit_end
);

} else {

$lista = json_encode(array());    

}
?>

var lista_edu = <?php echo $lista ?>;      

//+++++++++++++++++
//+   ELIMINAR    +
//+++++++++++++++++   

            
$('.link_eliminar_tipo_acceso').click(function(e){
  e.preventDefault();
  var ta_id = $(this).attr('href');
  
    // con apprise
    apprise('Desea eliminar el ta_id:' + ta_id, 
            {'verify':true}, 
            function(r) {
            // --    
            if(r) {   

                $.blockUI(config_blockui);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $apphost . "/admin/eliminarTipoAcceso" ?>',
                        dataType: "html",
                        data: 'ta_id=' + ta_id,
                        success: function(data){
                          $.unblockUI();
                          $('#tr_ta_id_' + ta_id).fadeOut();                                                       
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
    var ta_id = "";
	
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
                    ta_id = parseInt($(this).val());
                    checked.push(ta_id);
					//agregar remove
                    $('#tr_ta_id_' + ta_id).fadeOut().remove();
                });

                $.blockUI(config_blockui);

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $apphost . "/admin/eliminarVariosTipoAcceso" ?>',
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