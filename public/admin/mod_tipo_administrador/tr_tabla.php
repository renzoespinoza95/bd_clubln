<tr id="tr_tipo_administrador_id_<?php echo $tipo_administrador['tipo_administrador_id'] ?>">

<td>
    <input type="checkbox" name="check_eliminar[]" value="<?php echo $tipo_administrador['tipo_administrador_id'] ?>"  />
<?php  
  echo $tipo_administrador['tipo_administrador_id'];
?>
</td>

<td>
<?php  
  echo $tipo_administrador['descripcion'];
?>
</td>

<td>
<?php  
  echo $tipo_administrador['titulo'];
?>
</td>

<td>
<?php include "boton_on_off.php";?>
</td>




    
<td>
<?php include "botones_acciones.php";?>   
  
</td>
</tr>