<div id="modal_agregar" class="modal hide fade fullscreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Submenu</h3>
  </div>
  <div class="modal-body">
<?php
boot::uniform();
boot::chosen();
?>
<div class="row-fluid">

<div class="span12">


<form class="well uniForm" method="post" action="<?php echo $apphost ?>/admin/agregarSubmenu" enctype="multipart/form-data"> 
        
<?php

// Campo “Titulo”
echo '<label for="txt_titulo">Titulo</label>';
echo h2::txt(array(
    'id'           => 'txt_titulo',
    'name'         => 'txt_titulo',
    'type'         => 'text',
    'value'        => '',
    'class'        => 'span12 required',
    'autocomplete' => 'off',
));

// Campo “Url”
echo '<label for="txt_url">Url</label>';
echo h2::txt(array(
    'id'           => 'txt_url',
    'name'         => 'txt_url',
    'type'         => 'text',
    'value'        => '',
    'class'        => 'span12',
    'autocomplete' => 'off',
));

// Select “Menu”
echo '<label for="cbo_menu_id_agregar">Menu</label>';
echo h2::cbo(array(
    'id'                      => 'cbo_menu_id_agregar',
    'name'                    => 'cbo_menu_id_agregar',
    'class'                   => 'span3',
    'lista_item_clasico'      => menu::lista_menu(),
    'item_clasico_id'         => 'menu_id',
    'item_clasico_descripcion'=> 'titulo',
    'palabra_latina'          => 1,
    'item_seleccionado_id'    => null,
));

// Select “Target”
echo '<label for="txt_target">Target</label>';
echo h2::cbo(array(
    'id'                      => 'txt_target',
    'name'                    => 'txt_target',
    'lista_item_clasico'      => submenu::lista_target(),
    'item_clasico_id'         => 'cod_target',
    'item_clasico_descripcion'=> 'descripcion',
    'item_seleccionado_id'    => null,
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

