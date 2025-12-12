<?php

boot::uniform();

?>
<div class="row">

<div class="span4">
   <h3>Editar Tipo Acceso</h3>

<form class="form uniForm" method="post" action="<?php echo $apphost ?>/admin/editarTipoAcceso">


<input type="hidden" id="txt_ta_id" name="txt_ta_id" value="<?php echo $ta_id ?>" />
<?php
// Campo “Nombre”
echo '<label for="txt_nombre">Nombre</label>';
echo h2::txt(array(
    'id'           => 'txt_nombre',
    'name'         => 'txt_nombre',
    'type'         => 'text',
    'value'        => util::mostrar_palabra_latina($detalle_tipo_acceso['nombre']),
    'class'        => 'span3 required',
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
  document.getElementById('btn_cancelar').addEventListener('click', function(e) {
    e.preventDefault();
    jQuery(document).trigger('close.facebox');
  });
</script>