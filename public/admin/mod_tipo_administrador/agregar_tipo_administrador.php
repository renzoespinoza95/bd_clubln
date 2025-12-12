<div id="modal_agregar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Agregar Tipo Administrador</h3>
  </div>
  <div class="modal-body">
 


<?php

boot::uniform();
boot::img_input();

?>
<div class="row">

<div class="span5">


<form class="well uniForm" method="post" action="<?php echo $apphost ?>/admin/agregarTipoAdministrador" enctype="multipart/form-data"> 
        
<?php

// Campo “Descripcion”
echo '<label for="txt_descripcion">Descripcion</label>';
echo h2::txt(array(
    'id'           => 'txt_descripcion',
    'name'         => 'txt_descripcion',
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

