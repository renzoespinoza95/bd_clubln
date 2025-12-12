<?php
boot::uniform();
?>
<div id="modal_agregar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Administrador</h3>
  </div>
  <div class="modal-body">


<div class="row">

<div class="span5">
<form class="well uniForm" method="post" action="<?php echo $apphost ?>/admin/agregarAdministrador">
        <label>Nombres y apellidos</label>
        <input name="txt_nombres_apellidos" id="txt_nombres_apellidos" type="text" value="" class="span3 required">
        
        <label>Email</label>
        <input name="txt_email" id="txt_email" type="text" value="" class="span3 required">
        
        <label>Password</label>
        <input name="txt_password" id="txt_password" type="text" value="123" class="span3 required">
        

         <?php

// Select “Tipo Administrador”
echo '<label for="cbo_tipo_administrador_id_agregar">Tipo Administrador</label>';
echo h2::cbo(array(
    'id'                       => 'cbo_tipo_administrador_id_agregar',
    'name'                     => 'cbo_tipo_administrador_id_agregar',
    'class'                    => 'span3',
    'lista_item_clasico'       => tipo_administrador::lista_tipo_administrador(),
    'item_clasico_id'          => 'tipo_administrador_id',
    'item_clasico_descripcion' => 'descripcion',
    'item_seleccionado_id'     => null,
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