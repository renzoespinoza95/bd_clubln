<table class="table table-striped">
    <thead>
    <tr>
    <th class="span1">Id</th>
    <th class="span3">Titulo</th>
    <th class="span2">Orden</th>
	<th class="span2">Tipo admin</th>
    <th></th>
    </tr>
    </thead>
    <tbody>
<?php
foreach(menu::lista_menu_clasico() as $menu) {    
	include "tr_tabla.php";
}
include "tabla_js.php";
?>    
    </tbody>
    </table>