<?php

boot::uniform();

?>
<div class="row">

<div class="span4">

<h3>
   Editar variable sistema
</h3>   

<form class="form uniForm" method="post" 
action="<?php echo $apphost ?>/admin/editarVariablesSistema">


<input type="hidden" id="txt_nombre_variable_id" name="txt_nombre_variable_id" value="<?php echo $nombre_variable ?>" />
<?php
// Campo “nombre_variable”
echo h2::txt(array(
    'id'           => 'txt_variables_sistema_id_ed',
    'name'         => 'txt_variables_sistema_id_ed',
    'type'         => 'text',
    'value'        => $variables_sistema_id,
    'class'        => 'span3',
    'autocomplete' => 'off',
));
echo '<label for="txt_nombre_variable">nombre_variable</label>';
echo h2::txt(array(
    'id'           => 'txt_nombre_variable',
    'name'         => 'txt_nombre_variable',
    'type'         => 'text',
    'value'        => $detalle_variables_sistema['nombre_variable'],
    'class'        => 'span3',
    'autocomplete' => 'off',
));

// Campo “valor”
echo '<label for="txt_valor">valor</label>';
echo h2::txt(array(
    'id'           => 'txt_valor',
    'name'         => 'txt_valor',
    'type'         => 'text',
    'value'        => util::mostrar_palabra_latina($detalle_variables_sistema['valor']),
    'class'        => 'span3',
    'autocomplete' => 'off',
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
  $('#btn_cancelar').click(function(e) {
    e.preventDefault();
    jQuery(document).trigger('close.facebox');
  });
</script>
