<?php
$total_registros = tipo_administrador::cantidad_tipo_administrador();
if($total_registros > 0) {
    $paginacion = new Paginator($total_registros,9); 
    echo $paginacion->display_pages();   
} else {
?> 
<div class="alert alert-error">
        <button class="close" data-dismiss="alert">×</button>
        <i class="fa fa-exclamation"></i> No hay registros en esta consulta
</div>   

<?php
}
?>

<table class="table table-striped">
    <thead>
    <tr>
    <th class="span1">ID</th>
    <th class="span3">Descripción</th>
    <th class="span3">Submenu</th>
    <th class="span3">Activo</th>        
    <th></th>
    </tr>
    </thead>
    <tbody>
<?php
if($total_registros > 0) {
foreach(tipo_administrador::lista_tipo_administrador_paginacion($paginacion->limit_start, $paginacion->limit_end) as $tipo_administrador) { 
    include "tr_tabla.php";
?>
<?php
    }
}    
?>   

    </tbody>
    </table>
<?php
if($total_registros > 0) {
    echo $paginacion->display_pages();
} 
include "tabla_js.php";
?>