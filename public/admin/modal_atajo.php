<div id="modal_atajo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Atajo</h3>
  </div>
  <div class="modal-body">

<div class="row">

<div class="span5">

  <div>
    <input type="text" placeholder="Título Submenu" 
    data-qjs=".tbl_atajo tbody tr" autofocus="" 
    class="filtering form-control span5">
  </div>
    <table class="tbl_atajo table table-bordered table-striped">
      <thead class="thead-dark">
        <tr>
          <td>Menu</td>
          <td>URL</td>
        </tr>
      </thead>
      <tbody>
<?php
foreach(submenu::lista_submenu() as $submenu) {
?>
    <tr>
        <td>
<a href="<?php echo $apphost . $submenu['url'] ?>">          
<?php 
echo util::mostrar_palabra_latina($submenu['titulo'])
?>     
</a>     
        </td>
        <td>
<?php 
echo $submenu['url']
?>                    
        </td>
    </tr>
<?php
}
?>
      </tbody>
    </table>
  </div>  

</div>

</div>


</div>  

<?php
boot::filtering();
?>

<style type="text/css">
.uniForm #errorMsg h3 {
  color: white;
}  

.uniForm input {
  color: black !important;
}
</style>

