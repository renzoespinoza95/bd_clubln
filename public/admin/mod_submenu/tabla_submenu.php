<?php
$total_registros = submenu::cantidad_submenu();
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
<?php
    include "th_tabla.php";
?>
    <tbody>
<?php
if($total_registros > 0) {
    $lista_submenu = submenu::lista_submenu_paginacion($paginacion->limit_start, $paginacion->limit_end);
foreach($lista_submenu as $submenu) { 
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