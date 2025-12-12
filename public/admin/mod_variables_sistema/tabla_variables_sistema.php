<?php
boot::datatables();
?>
<table id="tbl_variables_sistema" class="table datatable">
    <thead>
    <tr>

    <th class="span1">nombre_variable</th>
    <th class="span4">valor</th>

    
<th class="span1"></th>
    </tr>
    </thead>
    <tbody>
<?php
foreach(variables_sistema::lista_variables_sistema() as $variables_sistema) { 
    include "tr_tabla.php";
}
?>    
    </tbody>
    </table>
<?php include "tabla_js.php" ?>  