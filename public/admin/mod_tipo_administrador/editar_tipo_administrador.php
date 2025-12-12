<?php
boot::uniform();
?>
<div class="row-fluid">

<div class="span12">

<form class="form uniForm" method="post" action="<?php echo $apphost ?>/admin/editarTipoAdministrador">
<h3>Editar tipo administrador</h3>

<input type="hidden" id="txt_tipo_administrador_id" name="txt_tipo_administrador_id" value="<?php echo $tipo_administrador_id ?>" />
<?php
// Campo “Descripcion”
echo '<label for="txt_descripcion">Descripcion</label>';
echo h2::txt(array(
    'id'           => 'txt_descripcion',
    'name'         => 'txt_descripcion',
    'type'         => 'text',
    'value'        => util::mostrar_palabra_latina($detalle_tipo_administrador['descripcion']),
    'class'        => 'span12 required',
    'autocomplete' => 'off',
));
//dd("det", $detalle_tipo_administrador["submenu_inicio"]);
?>
<label>Submenu inicio</label>
<?php
echo h2::cbo(array(
    'id' => 'cbo_submenu_inicio_agregar',
    'name' => 'cbo_submenu_inicio_agregar',
    'lista_item_clasico' => submenu::lista_submenu(),
    'item_clasico_id' => 'submenu_id',
    'item_clasico_descripcion' => 'titulo',
    'item_seleccionado_id' => $detalle_tipo_administrador["submenu_inicio"], // O coloca un ID si deseas que uno esté seleccionado
    'width' => '100%'
));
?>

<div class="form-actions">
             <button type="button" class="btn btn-warning" id="btn_cancelar"><i class="fa fa-mail-reply"></i> Cancelar</button>   
            <button class="btn btn-primary" type="submit">
            <i class="fa fa-save"> </i> Guardar</button>  
<button type="button" class="btn btn-danger">
                <i class="fa fa-thumbs-o-down"></i> Eliminar</button>
</div>

</form>

</div>
</div>