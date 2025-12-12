<tr id="tr_nombre_variable_<?php echo $variables_sistema['nombre_variable'] ?>">

<td>
<?php  
  echo $variables_sistema['nombre_variable'];
?>
</td>  

<td>
<?php  
  echo util::mostrar_palabra_latina($variables_sistema['valor']);
?>
</td> 


    
<td>
<?php include "botones_acciones.php" ?>   
  
</td>
</tr>