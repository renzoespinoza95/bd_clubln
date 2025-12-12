<div id="modal_agregar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Variables Sistema</h3>
  </div>
  <div class="modal-body">    
<?php
boot::uniform();
?>
<div class="row-fluid">

<div class="span12">
<form class="well uniForm" method="post" action="<?php echo $apphost ?>/admin/agregarVariablesSistema" >
        
<?php

// campo “nombre_variable”
echo '<label for="txt_nombre_variable">nombre_variable</label>';
echo h2::txt([
    'id'          => 'txt_nombre_variable',
    'name'        => 'txt_nombre_variable',
    'type'        => 'text',           // antes 'input' => 'text'
    'value'       => '',
    'class'       => 'span12 required',
    'autocomplete'=> 'off'             // opcional, si quieres mantener el comportamiento por defecto
]);

// campo “valor”
echo '<label for="txt_valor">valor</label>';
echo h2::txt([
    'id'          => 'txt_valor',
    'name'        => 'txt_valor',
    'type'        => 'text',
    'value'       => '',
    'class'       => 'span12 required',
    'autocomplete'=> 'off'
]);



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


