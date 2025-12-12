<!doctype>
<html>
<head>
  <meta charset="utf-8">

    <title>Aplicacion</title>
    
    <link href="css/uni-form.css" media="screen" rel="stylesheet"/>
    <link href="css/blue.uni-form.css" title="Default Style" media="screen" rel="stylesheet"/>
    <link href="css/core.css" media="screen" rel="stylesheet"/>
    

  </head>

  <body>

    <form action="../app_usuario/agregar" method="post" class="uniForm">
            
      <fieldset>
        <h3>Registro de Usuario</h3>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Nombres</label>
          <input name="nombres" id="nombres" data-default-value="Escribir el nombre" size="35" maxlength="50" type="text" class="textInput required"/>
          <p class="formHint">Es necesario el nombre.</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Apellido Paterno</label>
          <input name="ap_paterno" id="ap_paterno" data-default-value="Escribir el apellido paterno" size="35" maxlength="50" type="text" class="textInput required"/>
          <p class="formHint">Es necesario el apellido paterno.</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Apellido Materno</label>
          <input name="ap_materno" id="ap_materno" data-default-value="Escribir el apellido materno" size="35" maxlength="50" type="text" class="textInput required"/>
          <p class="formHint">Es necesario el apellido materno</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Código Universitario</label>
          <input name="codigo" id="codigo" data-default-value="Escribir el código universitario" size="35" maxlength="50" type="text" class="textInput required"/>
          <p class="formHint">Es necesario el código universitario</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Télefono</label>
          <input name="telefono" id="telefono" data-default-value="Escribir el número telefónico" size="35" maxlength="50" type="text" class="textInput required"/>
          <p class="formHint">Es necesario un número telefónico/celular</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Password</label>
          <input name="password" id="password" size="35" maxlength="50" type="password" class="textInput required"/>
          <p class="formHint">Ingrese aqu&iacute; su password.</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Activo</label>
          <select id="activo" name="activo">
            <option value="1" selected="selected">Si</option>
            <option value="0">No</option>
          </select>
          
          <p class="formHint">Cambiar activación</p>
        </div>       
    
         <div class="ctrlHolder">
          <label for=""><em>*</em> E-mail</label>
          <input name="email" id="email" data-default-value="Escriba el email" size="35" maxlength="50" type="text" class="textInput required validateEmail"/>
          <p class="formHint">Necesito un email válido</p>
        </div>
        
        <div class="ctrlHolder">
          <label for=""><em>*</em> Género</label>
          <select id="genero" name="genero">
            <option value="masculino" selected="selected">Masculino</option>
            <option value="femenino">Femenino</option>
          </select>
          <p class="formHint">Necesito un género válido</p>
        </div>
      
        <div class="ctrlHolder">
          <p class="label">Acepto las condiciones de uso</p>
          <ul class="blockLabels">
            <li><label for=""><input type="checkbox" name="agreement" class="required"> He leído las instrucciones de uso.</label></li>
          </ul>
        </div>

      </fieldset>
      
      <div class="buttonHolder">
        <button type="submit" class="primaryAction">Registrar</button>
      </div>

    </form>

  <!-- lib Dragan -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" language="javaScript"></script>
    <script type="text/javascript" src="js/uni-form-validation.jquery.js" charset="utf-8"></script>
    <script type="text/javascript" src="localization/es.js" charset="utf-8"></script>
    <script type="text/javascript">
      $(function(){
        $('form.uniForm').uniform({
          prevent_submit : true
        });
      });
    </script>
  </body>
</html>