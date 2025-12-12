<div class="row">

<div class="span5">

<h3>
Editar Administrador
</h3>

<form class="well uniForm" method="post" action="<?php echo $apphost ?>/editarAdministrador">
        <label>Nombres y apellidos</label>
        <input name="AdminTxtNombreApellidos" id="AdminTxtNombreApellidos" type="text" value="<?php echo util::mostrar_palabra_latina($detalle_administrador['nombres_apellidos']) ?>" class="span3 required">
        
        <input type="hidden" name="AdminTxtAdministradorId" id="AdminTxtAdministradorId" class="required" value="<?php echo $administrador_id ?>" />
        
        
       <label>Email</label>
        <input name="AdminTxtEmail" id="AdminTxtEmail" type="text" value="<?php echo util::mostrar_html($detalle_administrador['email']) ?>" class="span3 required">
        
        <label>Password</label>
        <input name="AdminTxtPassword" id="AdminTxtPassword" type="text" value="<?php echo $detalle_administrador['clavel'] ?>" class="span3 required">       
     
        
<?php

// Select “Tipo Administrador” (edición)
echo '<label for="cbo_tipo_administrador_id_editar">Tipo Administrador</label>';
echo h2::cbo(array(
    'id'                       => 'cbo_tipo_administrador_id_editar',
    'name'                     => 'cbo_tipo_administrador_id_editar',
    'class'                    => 'span3',
    'lista_item_clasico'       => tipo_administrador::lista_tipo_administrador(),
    'item_clasico_id'          => 'tipo_administrador_id',
    'item_clasico_descripcion' => 'descripcion',
    'item_seleccionado_id'     => $detalle_administrador['tipo_administrador_id'],
));


?>

                
<div class="form-actions">

    <button class="btn btn-warning" id="btn_cancelar">
      <i class="fa fa-mail-reply"></i> Cancelar
    </button>

    <button class="btn btn-primary" type="submit">
			<i class="fa fa-save"></i> Guardar
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
