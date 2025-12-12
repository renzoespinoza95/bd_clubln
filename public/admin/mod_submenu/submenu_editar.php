<?php
boot::uniform();
?>
<div class="row-fluid">

<div class="span12">
<form class="form uniForm" method="post" action="<?php echo $apphost ?>/admin/editarSubmenu">
<h3>Editar submenu</h3>

<input type="hidden" id="txt_submenu_id" name="txt_submenu_id" value="<?php echo $submenu_id ?>" />
<?php

// Campo “Titulo”
echo '<label for="txt_titulo">Titulo</label>';
echo h2::txt(array(
    'id'           => 'txt_titulo',
    'name'         => 'txt_titulo',
    'type'         => 'text',
    'value'        => util::mostrar_palabra_latina($detalle_submenu['submenu_titulo']),
    'class'        => 'span12 required',
    'autocomplete' => 'off',
));

// Campo “Url”
echo '<label for="txt_url">Url</label>';
echo h2::txt(array(
    'id'           => 'txt_url',
    'name'         => 'txt_url',
    'type'         => 'text',
    'value'        => $detalle_submenu['url'],
    'class'        => 'span12 required',
    'autocomplete' => 'off',
));

// Select “Menu”
echo '<label for="cbo_menu_id_editar">Menu</label>';
echo h2::cbo(array(
    'id'                      => 'cbo_menu_id_editar',
    'name'                    => 'cbo_menu_id_editar',
    'lista_item_clasico'      => menu::lista_menu(),
    'item_clasico_id'         => 'menu_id',
    'item_clasico_descripcion'=> 'titulo',
    'palabra_latina'          => 1,
    'item_seleccionado_id'    => $detalle_submenu['menu_id'],
));

// Select “Target”
echo '<label for="cbo_target_editar">Target</label>';
echo h2::cbo(array(
    'id'                      => 'cbo_target_editar',
    'name'                    => 'cbo_target_editar',
    'lista_item_clasico'      => submenu::lista_target(),
    'item_clasico_id'         => 'cod_target',
    'item_clasico_descripcion'=> 'descripcion',
    'item_seleccionado_id'    => $detalle_submenu['target'],
));
?>

<div class="form-actions">
    <button class="btn btn-warning" id="btn_cancelar">
      <i class="fa fa-mail-reply"></i> Cancelar
    </button>
    <button class="btn btn-primary" type="submit">
            <i class="fa fa-save"> </i> Guardar
    </button>  
</div>
</form>

</div>
</div>
<script type="text/javascript">
  document.getElementById('btn_cancelar').addEventListener('click', function(e) {
    e.preventDefault();
    jQuery(document).trigger('close.facebox');
  });
</script>