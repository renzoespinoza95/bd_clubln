<p>
	<h3>Lista de Menu</h3>

	<a href="#modal_agregar" role="button" class="btn btn-success" data-toggle="modal">
        <i class="fa fa-plus">	
        </i> Agregar
    </a>

      <a href="#" class="btn btn-info" id="btn_actualizar_orden">
  <i class="fa fa-refresh icon-white"></i> Orden
  </a> 
			<a href="<?php echo $apphost . "/admin/listaMenu" ?>" class="btn btn-info" >
		<i class="fa fa-home"></i> Inicio
	</a> 
</p>
<?php 
include "agregar_menu.php"; 
?>
<!-- Modal cambiar orden de MENÚ -->
<div id="modalOrdenMenu" class="modal hide fade" tabindex="-1">
  <div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Cambiar orden de menú</h3>
  </div>

  <div class="modal-body">
    <label for="inputOrdenMenu">Nuevo orden:</label>
    <input type="number" min="1" id="inputOrdenMenu" class="input-small">
  </div>

  <div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cancelar</button>
    <button id="btnGuardarOrdenMenu" class="btn btn-primary">Guardar</button>
  </div>
</div>

