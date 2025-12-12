<div id="modal_agregar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Tipo Acceso</h3>
  </div>
  <div class="modal-body">
 


<?php

boot::uniform();
boot::img_input();

?>
<div class="row">

<div class="span5">


<form class="well uniForm" method="post" action="<?php echo $apphost ?>/admin/agregarTipoAcceso" enctype="multipart/form-data"> 
        
<?php
// Campo “Nombre”
echo '<label for="txt_nombre">Nombre</label>';
echo h2::txt(array(
    'id'           => 'txt_nombre',
    'name'         => 'txt_nombre',
    'type'         => 'text',
    'value'        => '',
    'class'        => 'span3 required',
    'autocomplete' => 'off',
));

?>
                
</div>
</div>

<!-- -->    
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-mail-reply"></i> Cancelar</button>
    <button class="btn btn-primary">
        <i class="fa fa-save"></i> Guardar</button>
  </div>
</div>
</form>
<?php
boot::final_chosen();
?>
</script>
<style type="text/css">
.uniForm #errorMsg h3 {
  color: white;
}  

.uniForm input {
  color: black !important;
}
</style>


