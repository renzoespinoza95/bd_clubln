<?php
boot::facebox();
?>
<table class="table table-striped">
  <?php include "th_tabla.php" ?> 
    <tbody>
<?php
foreach(administradortbl::lista_administradores() as $administrador) { 
include "tr_tabla.php"
?>    

<?php
}
?>    
    </tbody>
    </table>
<?php
include "tabla_js.php";
?>    