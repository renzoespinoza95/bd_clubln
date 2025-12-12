<?php

class variables_sistema {

 public static function lista_variables_sistema($qw = null)
{
    $query = <<<EOF
    SELECT * FROM variables_sistema $qw 
EOF;

$res = DB::query($query);

return $res;          
}        

  public static  function agregar_variables_sistema(
$nombre_variable,
$valor)
{
    DB::insert('variables_sistema', array(
      'nombre_variable' => $nombre_variable,
      'valor' => $valor
     
));    
    $res = DB::insertId();    
    return $res;
}            
 public static function detalle_variables_sistema($nombre_variable)
{            
    $query = <<<EOF
SELECT * FROM variables_sistema WHERE nombre_variable = '$nombre_variable'
EOF;

    $res = DB::queryFirstRow($query);
    return $res;    
} 

 public static function variable_sistema($nombre_variable)
{            
    $query = <<<EOF
SELECT * FROM variables_sistema WHERE nombre_variable = '$nombre_variable'
EOF;

    $res = DB::queryFirstRow($query);
    return $res['valor'];    
}

public static function variable($nombre_variable)
{            
    $query = <<<EOF
    SELECT * FROM variables_sistema WHERE nombre_variable = '$nombre_variable'
EOF;

    $detalle = DB::queryFirstRow($query);
    $res = $detalle['valor'] ;
    
    return $res;    
}

public static function editar_variables_sistema($nombre_variable, $nombre_campo, $valor)
{
  DB::update('variables_sistema', array(
  $nombre_campo => $valor
  ), "nombre_variable=%s", $nombre_variable);
}  

public static function eliminar_variables_sistema($nombre_variable)
{   
    DB::delete('variables_sistema', 'nombre_variable=%s', $nombre_variable);
}  


public static function cantidad_variables_sistema($qw)
{            
    $query = <<<EOF
SELECT count(*) as cant FROM variables_sistema $qw
EOF;

    $res = DB::queryFirstRow($query);
    return $res['cant'];    
}       

public static function verificar_api_key($app, $req = "") {
    
    $cliente_api_key = $app->request()->params('API_KEY');


    switch ($req) {
        case "":
            $cliente_api_key = $app->request()->params('API_KEY');
            break;
        case "POST":
            $request = Slim::getInstance()->request();
            $cliente_api_key = json_decode($request->getBody());            
            $cliente_api_key = (array) $cliente_api_key;
                        
            $cliente_api_key = $cliente_api_key['API_KEY'];  
            break;
        case "PUT":
            $request = Slim::getInstance()->request();
            $cliente_api_key = json_decode($request->getBody());
            $cliente_api_key = (array) $cliente_api_key;
            
            $cliente_api_key = $cliente_api_key['API_KEY'];            
            break;
    }




    $sistema_api_key = api_variables_sistema::variable("API_KEY");

    if($cliente_api_key != $sistema_api_key ||
            $cliente_api_key == "") {

        $respuesta = array(
                "response" => array(
                    "tipo" => "ERROR",
                    "descripcion" => "No esta Autorizado para usar la API"                
            )); 
        echo json_encode($respuesta);   
        exit;
    }
}

public static function verificar_api_key_imagen($cliente_api_key) {

    $sistema_api_key = api_variables_sistema::variable("API_KEY");

    if($cliente_api_key != $sistema_api_key ||
            $cliente_api_key == "") {

        $respuesta = array(
                "response" => array(
                    "tipo" => "ERROR",
                    "descripcion" => "No esta Autorizado para usar la API"                
            )); 
        echo json_encode($respuesta);   
        exit;
    }
}
          
//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++  
}


