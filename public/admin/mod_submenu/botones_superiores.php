<p>
<h3>Lista submenu</h3>
	<a href="#modal_agregar" role="button" class="btn btn-success" data-toggle="modal">
        <i class="fa fa-plus">	
        </i> Agregar
    </a>

<a href="<?php echo $apphost ?>/admin/listaSubmenu" class="btn btn-info" target="_self">
        <i class="fa fa-refresh">	
        </i> Actualizar
    </a> 

	<a href="#modal_buscar" class="btn btn-info" data-toggle="modal">
		<i class="icon-white icon-search"></i> Buscar
	</a> 
</p>

<div id="tabla_grado_titulo_docente">   

	<div>
<!-- modal agregar -->
		<?php 
		include "submenu_agregar.php"; 
		include "submenu_buscar.php";
		?>
<!-- --> 
 
	</div>
</div>
<?php
boot::apprise();
?>
<!-- Modal cambiar orden -->
<div id="modalOrdenSubmenu" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalOrdenLabel">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="modalOrdenLabel">Cambiar orden de sub-menú</h3>
  </div>

  <div class="modal-body">
    <label for="inputOrden" class="control-label">Nuevo orden:</label>
    <input type="number" min="1" id="inputOrden" class="input-small">
  </div>

  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cancelar</button>
    <button id="btnGuardarOrden" class="btn btn-primary">Guardar</button>
  </div>
</div>
