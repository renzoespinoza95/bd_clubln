<script type="text/javascript">

//+++++++++++++++++
//+   ELIMINAR    +
//+++++++++++++++++   
            
$('.link_eliminar_variables_sistema').click(function(e){
  e.preventDefault();

  var $link = $(this);                             // guardamos la referencia
  var variables_sistema_id = $link.attr('rel');

  apprise(
    '¿Desea eliminar el variables_sistemas_id: ' + variables_sistema_id + '?',
    { 'verify': true },
    function(r) {
      if (r) {
        $.blockUI(config_blockui);
        $.ajax({
          type: 'POST',
          url: '<?php echo $apphost . "/admin/eliminarVariablesSistema" ?>',
          dataType: 'html',
          data: 'variables_sistema_id=' + variables_sistema_id,
          success: function(data){
            $.unblockUI();
            // ocultar con animación y luego remover el <tr>
            $link.closest('tr')
                 .fadeOut(300, function(){ $(this).remove(); });
          },
          error: function(){
            $.unblockUI();
            apprise('Error eliminando el registro', { okBtn: 'Cerrar' });
          }
        });
      }
    }
  );
});


$(document).ready(function(){
    $('#tbl_variables_sistema').DataTable({
        // opciones personalizadas aquí (opcional)
        paging: true,
        searching: true,        
    });
});




</script>  