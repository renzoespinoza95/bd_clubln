<?php
$query_where = "";

//=== FILTRO VENTADEL_ID ===
if($cbo_filtro == "menu_id") {
$total_registros = submenu::cantidad_submenu_por_menu_id($txt_criterio);
}
//=== FILTRO MONTO ===
if($cbo_filtro == "monto") {
$total_registros = mud_ventadel::cantidad_ventadel_por_monto($txt_criterio);
}
//=== FILTRO FECHA_CREACION ===
if($cbo_filtro == "fecha_creacion") {
$total_registros = mud_ventadel::cantidad_ventadel_por_fecha_creacion($txt_criterio);
}
//=== FILTRO VENTMER_ID ===
if($cbo_filtro == "ventmer_id") {
$total_registros = mud_ventadel::cantidad_ventadel_por_ventmer_id($txt_criterio);
}


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
exit;
}
?>
<table class="table table-striped">
<?php
	include $path_public . "/admin/mod_submenu/th_tabla.php";     	
?>	
    <tbody>
<?php
//=== COMBO VENTADEL_ID ===
if($cbo_filtro == 'menu_id') {
$lista_ventadel = submenu::lista_submenu_por_menu_id(
    $txt_criterio,
    $paginacion->limit_start, 
    $paginacion->limit_end);
}
//=== COMBO MONTO ===
if($cbo_filtro == 'monto') {
$lista_ventadel = mud_ventadel::lista_ventadel_por_monto(
    $txt_criterio,
    $paginacion->limit_start, 
    $paginacion->limit_end);
}
//=== COMBO FECHA_CREACION ===
if($cbo_filtro == 'fecha_creacion') {
$lista_ventadel = mud_ventadel::lista_ventadel_por_fecha_creacion(
    $txt_criterio,
    $paginacion->limit_start, 
    $paginacion->limit_end);
}
//=== COMBO VENTMER_ID ===
if($cbo_filtro == 'ventmer_id') {
$lista_ventadel = mud_ventadel::lista_ventadel_por_ventmer_id(
    $txt_criterio,
    $paginacion->limit_start, 
    $paginacion->limit_end);
}


//var_dump($cbo_filtro);
//var_dump($lista_dependencia);
//exit;
if($total_registros > 0) {

foreach($lista_ventadel as $submenu) {    

    // dd($lista_ventadel);
	include $path_public . "/admin/mod_submenu/tr_tabla.php";     
}

}
?>    
    </tbody>
    </table>
<?php
if($total_registros > 0) {
    echo $paginacion->display_pages();
} 
include $path_public . "/admin/mod_submenu/tabla_js.php";     
?>