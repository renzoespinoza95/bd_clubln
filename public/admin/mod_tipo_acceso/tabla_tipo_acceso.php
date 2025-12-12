<?php
$total_registros = tipo_acceso::cantidad_tipo_acceso();
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
    <th class="span3">Nombre</th>     
    <th></th>
    </tr>
    </thead>
    <tbody>
<?php
if($total_registros > 0) {
foreach(tipo_acceso::lista_tipo_acceso_paginacion($paginacion->limit_start, $paginacion->limit_end) as $tipo_acceso) { 
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

boot::css_pagination();
include "tabla_js.php";
?>