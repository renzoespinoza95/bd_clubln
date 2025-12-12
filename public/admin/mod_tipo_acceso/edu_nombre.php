<script type="text/javascript">

$(document).ready(function() {
//--- INICIO ---
$('.link_edu_nombre').click(function() {

  var self = $(this);
  var edu_id = self.attr('rel'); 

  console.log(edu_id);
  var texto = _.findWhere(lista_edu, 
    { ta_id: parseInt(edu_id) });

  $('#btn_edu_nombre_guardar').attr("data-btn", edu_id);
  console.log(texto);

  $('#txt_edu_nombre').val(texto.nombre);
  

});  


//---

$('#btn_edu_nombre_guardar').click(function() {

          $.blockUI(config_blockui);

          var texto = $('#txt_edu_nombre').val();  
          var edu_id = $(this).attr("data-btn");


                $.ajax({
                    type: 'POST',
                    url: '<?php echo $apphost ?>/admin/eduTipoAccesoNombre',
                    dataType: "json",
                    data: {
                        txt_edu_ta_id: edu_id,
                        txt_edu_nombre: texto
                    },
                    success: function(data) {
                        $('#link_edu_nombre_' + edu_id).html(texto);
                        $.unblockUI();
                        mostrar_mensaje_superior("success", "Registros editado exitosamente");
                        
                    }

                });

});  


//--- FIN ---
});


  
</script>
<div id="modal_edu_nombre" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Edu nombre</h3>
  </div>
  <div class="modal-body">

<div class="row">

<div class="span5">

<form class="well uniForm">
       
<input type="text" id="txt_edu_nombre" name="txt_edu_nombre" value="" />     

                
</div>
</div>


  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-mail-reply"></i> Cancelar</button>
    <button class="btn btn-primary" data-btn="0" id="btn_edu_nombre_guardar" data-dismiss="modal" 
    aria-hidden="true">
        <i class="fa fa-save"></i> Guardar</button>
  </div>
</div>
</form>
