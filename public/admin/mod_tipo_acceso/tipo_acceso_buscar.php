<div id="modal_buscar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Buscar Tipo Acceso</h3>
  </div>
  <div class="modal-body">
<?php
boot::uniform();
?>
<div class="row">

<div class="span5">

<div class="tabbable" style="margin-bottom: 18px;">
<!-- INICIO ENCABEZADOS -->
       <ul class="nav nav-tabs">
        <li class="active">
          <a href="#tab1" data-toggle="tab">ID</a>
        </li>        
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Filtros <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="#tab2" data-toggle="tab">nombre</a></li>       
          </ul>
        </li>
      </ul>
<!-- FIN ENCABEZADOS --> 

<!-- INICIO FORMULARIOS -->     
        <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
<!-- INICIO TAB1 -->          
          <div class="tab-pane active" id="tab1">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarTipoAcceso">
              <label>buscar por id</label>
              <input type="text" id="cbo_filtro" name="cbo_filtro" value="TA_ID" class="span2" readonly="true" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
            </form>
          </div>      
<!-- INICIO TAB2 -->
          <div class="tab-pane" id="tab2">
            <form class="well" method="post" action="<?php echo $apphost ?>/admin/buscarTipoAcceso">
             <label>buscar por nombre</label>
             <input type="text" id="cbo_filtro" 
             readonly="true" name="cbo_filtro" value="NOMBRE" class="span2" />
              <input type="text" id="txt_criterio" name="txt_criterio" value="" class="span2" autocomplete="off" />
            
            <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Buscar</button>
      </form>
          </div>       


        </div>
      </div>
<!-- FIN FORMULARIOS -->
</form> 


                
</div>
</div>

 <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-mail-reply"></i> Cancelar</button>    
  </div>

<!-- -->    
  </div>

</div>

