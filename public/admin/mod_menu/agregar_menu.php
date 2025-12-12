<div id="modal_agregar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Menu</h3>
  </div>
  <div class="modal-body">
<?php
  boot::uniform();
?>
<div class="row">

<div class="span5">

<form class="well" method="post" action="<?php echo $apphost ?>/admin/agregarMenu">
        <label>Titulo</label>
        <input name="txt_titulo" id="txt_titulo" type="text" value="" class="span3">               

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