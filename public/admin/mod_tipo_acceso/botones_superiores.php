<p>
<?php
helpers::crear_titulo(array(
'titulo'=>'Lista de Tipo Acceso'
));
?> 

	<a href="#modal_agregar" role="button" class="btn btn-success" data-toggle="modal">
        <i class="fa fa-plus">	
        </i> Agregar
    </a>

	<a href="#" class="btn btn-warning" id="btn_eliminar_varios">
		<i class="icon-white icon-trash"></i> Eliminar
	</a> 

<a href="<?php echo $apphost ?>/admin/listaTipoAcceso" class="btn btn-info" target="_self">
        <i class="fa fa-refresh">	
        </i> Actualizar
    </a> 

	<a href="#modal_buscar" class="btn btn-info" data-toggle="modal">
		<i class="icon-white icon-search"></i> Buscar
	</a> 
</p>

<div id="tabla_tipo_acceso">   

	<div>
<!-- modal agregar -->
		<?php 
		include "tipo_acceso_agregar.php"; 
		include "tipo_acceso_buscar.php";
		?>
<!-- --> 
 
	</div>
</div>
<?php
boot::apprise();
?>
