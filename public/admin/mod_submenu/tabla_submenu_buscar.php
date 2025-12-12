<?php
$query_where = "";

if ($cbo_filtro == "TITULO") {
    $query_where = " WHERE titulo LIKE '%" . $txt_criterio . "%'";
}

if ($cbo_filtro == "URL") {
    $query_where = " WHERE url LIKE '%" . $txt_criterio . "%'";
}

if ($cbo_filtro == "ORDEN") {
    $query_where = " WHERE orden = " . $txt_criterio;
}

if ($cbo_filtro == "MENU_ID") {
    $query_where = " WHERE menu_id = " . $txt_criterio;
}

if ($cbo_filtro == "TARGET") {
    $query_where = " WHERE taget LIKE '%" . $txt_criterio . "%'";
}


$total_registros = submenu::cantidad_submenu($query_where);
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
<?php
    include "th_tabla.php";
?>
    <tbody>
<?php

if ($cbo_filtro == "TITULO") {
    $lista_submenu = submenu::lista_submenu_por_titulo($txt_criterio);
}

if ($cbo_filtro == "URL") {
    $lista_submenu = submenu::lista_submenu_por_url($txt_criterio);
}

if ($cbo_filtro == "ORDEN") {
    $lista_submenu = submenu::lista_submenu_por_orden($txt_criterio);
}

if ($cbo_filtro == "MENU_ID") {
    $lista_submenu = submenu::lista_submenu_por_menu_id($txt_criterio);
}

if ($cbo_filtro == "TARGET") {
    $lista_submenu = submenu::lista_submenu_por_target($txt_criterio);
}


var_dump($lista_submenu);
exit;


foreach($lista_submenu as $submenu) { 
   
   dd($lista_submenu);
    include "tr_tabla.php";

}
?>    
    </tbody>
    </table>
<?php
include "tabla_js.php";
?> 