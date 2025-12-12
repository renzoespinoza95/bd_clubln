<?php
$query_where = "";
if ($cbo_filtro == "TA_ID") {
    $query_where = " WHERE ta_id = " . $txt_criterio;
}

if ($cbo_filtro == "NOMBRE") {
    $query_where = " WHERE nombre LIKE '%" . $txt_criterio . "%'";
}

$total_registros = tipo_acceso::cantidad_tipo_acceso($query_where);
if($total_registros > 0) {
    $paginacion = new Paginator($total_registros,9); 
    //echo $paginacion->display_pages();   
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
if ($cbo_filtro == "TA_ID") {
    $lista_tipo_acceso = tipo_acceso::lista_tipo_acceso_por_ta_id($txt_criterio);
}

if ($cbo_filtro == "NOMBRE") {
    $lista_tipo_acceso = tipo_acceso::lista_tipo_acceso_por_nombre($txt_criterio);
}

/*
var_dump($lista_tipo_acceso);
exit;
*/

foreach($lista_tipo_acceso as $tipo_acceso) { 
   
    include "tr_tabla.php";

}
?>    
    </tbody>
    </table>
<?php
include "tabla_js.php";
?> 