<tr id="tr_administrador_id_<?php echo $administrador['administrador_id'] ?>">

<td>
<?php  
  echo $administrador['administrador_id'];
?>
</td>

<td>
<?php  
  echo util::mostrar_palabra_latina($administrador['nombres_apellidos']);
?>
</td>

<td>
<?php  
  echo $administrador['email'];
?>
</td>

<td>
<?php  
  echo $administrador['clavel'];
?>
</td>

<td>
<?php include "boton_on_off.php";?>
</td>   

<td>
<?php  
  echo $administrador['descripcion'];
?>
</td>

<td>
<?php  
  echo $administrador['titulo'];
?>
</td> 
    
<td>
<?php
    
// Enlace “Editar Administrador”
echo '<a ' . h2::parseAttributes(array(
    'href'  => $apphost . "/editarAdministrador/" . $administrador['administrador_id'],
    'class' => 'btn btn-info',
    'rel'   => 'facebox',
)) . '>'
   . '<i class="icon-white icon-pencil"></i>'
 . '</a>';

// Enlace “Eliminar Administrador”
echo '<a ' . h2::parseAttributes(array(
    'href'  => $administrador['administrador_id'],
    'class' => 'link_eliminar_administradortbl btn btn-info',
)) . '>'
   . '<i class="icon-white icon-remove"></i>'
 . '</a>';


?>   
</td>
</tr>