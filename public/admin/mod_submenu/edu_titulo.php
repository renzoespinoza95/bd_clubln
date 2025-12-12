<script type="text/javascript">
$(document).ready(function() {

    $('.link_edu_titulo').click(function() {
        var self = $(this);
        var edu_id = self.attr('rel'); 
        console.log(edu_id);
        var texto = $.trim($(this).text());
        $('#btn_edu_titulo_guardar').attr("data-btn", edu_id);
        console.log(texto);
        $('#txt_edu_titulo').val(texto);
    });  


    $('#btn_edu_titulo_guardar').click(function() {

        $.blockUI(config_blockui);

        var texto = $('#txt_edu_titulo').val();  
        var edu_id = $(this).attr("data-btn");

        $.ajax({
            type: 'POST',
            url: '<?php echo $apphost ?>/admin/eduSubmenuTitulo',
            dataType: "json",
            data: {
                txt_edu_submenu_id: edu_id,
                txt_edu_titulo: texto
            },
            success: function(data) {
                $('#link_edu_titulo_' + edu_id).html(texto);
                $.unblockUI();
                msg_exito();
            }
        });
    });  
});  
</script>
<div id="modal_edu_titulo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Edu titulo</h3>
      </div>
      <div class="modal-body">
          <div class="row-fluid">
              <div class="span12">
              <form class="well uniForm">             
                  <input type="text" id="txt_edu_titulo" name="txt_edu_titulo" value="" />     
              </form>                
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">
              <i class="fa fa-mail-reply"></i> Cancelar</button>
          <button class="btn btn-primary" data-btn="0" id="btn_edu_titulo_guardar" data-dismiss="modal" 
          aria-hidden="true">
              <i class="fa fa-save"></i> Guardar</button>
      </div>
</div>

