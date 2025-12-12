<div class="row-fluid">

<div class="span12">

<h3>Editar menu</div>

<form class="form" method="post" action="<?php echo $apphost ?>/admin/editarMenu">


<input type="hidden" id="txt_menu_id" name="txt_menu_id" value="<?php echo $menu_id ?>" />
<?php

echo '<label for="txt_titulo">Titulo</label>';
echo h2::txt(array(
    'id'           => 'txt_titulo',
    'name'         => 'txt_titulo',
    'type'         => 'text',
    'value'        => $detalle_menu['titulo'],
    'class'        => 'span8',
    'autocomplete' => 'off',
));

// Select “Tipo Administrador” (edición)
echo '<label for="cbo_tipo_administrador_id_editar">Tipo Administrador</label>';
echo h2::cbo(array(
    'id'                       => 'cbo_tipo_administrador_id_editar',
    'name'                     => 'cbo_tipo_administrador_id_editar',
    'class'                    => 'span8',
    'lista_item_clasico'       => tipo_administrador::lista_tipo_administrador(),
    'item_clasico_id'          => 'tipo_administrador_id',
    'item_clasico_descripcion' => 'descripcion',
    'item_seleccionado_id'     => $detalle_menu['tipo_administrador_id'],
));

?>


<div class="form-actions">
        <button class="btn btn-warning" id="btn_cancelar">
            <i class="fa fa-mail-reply"></i> Cancelar
        </button> 
            <button class="btn btn-primary" type="submit">
            <i class="fa fa-save"> </i> Guardar</button>  
</div>
</form>

</div>
</div>
<script type="text/javascript">    
$('#btn_cancelar').click(function(e) {
   e.preventDefault(); 
   jQuery(document).trigger('close.facebox');
});    
</script>