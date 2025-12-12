<?php
class amarilis {


//ej: $fecha = util::fecha_random('02/03/2022', '02/11/2022');
public static function fecha_random($fecha_inicio, $fecha_final) {
    // Separar día, mes y año de las fechas de entrada (dd/mm/yyyy)
    list($dia_i, $mes_i, $anio_i) = explode('/', $fecha_inicio);
    list($dia_f, $mes_f, $anio_f) = explode('/', $fecha_final);

    // Convertir a enteros
    $anio_i = (int)$anio_i;
    $anio_f = (int)$anio_f;
    $mes_i = (int)$mes_i;
    $mes_f = (int)$mes_f;
    $dia_i = (int)$dia_i;
    $dia_f = (int)$dia_f;

    // Generar un año aleatorio dentro del rango
    $anio_random = rand($anio_i, $anio_f);

    // Definir los meses disponibles según el año generado
    if ($anio_random == $anio_i) {
        $mes_min = $mes_i;
    } else {
        $mes_min = 1;
    }

    if ($anio_random == $anio_f) {
        $mes_max = $mes_f;
    } else {
        $mes_max = 12;
    }

    // Generar un mes aleatorio dentro del rango permitido
    $mes_random = rand($mes_min, $mes_max);

    // Definir los días disponibles según el mes y el año
    if ($anio_random == $anio_i && $mes_random == $mes_i) {
        $dia_min = $dia_i;
    } else {
        $dia_min = 1;
    }

    if ($anio_random == $anio_f && $mes_random == $mes_f) {
        $dia_max = $dia_f;
    } else {
        $dia_max = cal_days_in_month(CAL_GREGORIAN, $mes_random, $anio_random);
    }

    // Generar un día aleatorio dentro del rango permitido
    $dia_random = rand($dia_min, $dia_max);

    // Asegurar que los valores tengan 2 dígitos cuando corresponda
    $mes_random = str_pad($mes_random, 2, "0", STR_PAD_LEFT);
    $dia_random = str_pad($dia_random, 2, "0", STR_PAD_LEFT);

    // Formar la fecha final en formato YYYY-MM-DD
    return $anio_random . '-' . $mes_random . '-' . $dia_random;
}

public static function hora_random() {
    // Convert to timetamps
    $min = strtotime('2019-01-01');
    $max = strtotime('2019-01-02');

    // Generate random number using above bounds
    $val = rand($min, $max);

    // Convert back to desired date format
    return date('H:i:s', $val);
}

public static function min_max_hora_random($hora_inicio, $hora_termino) {

    $min = strtotime('2019-01-01 ' . $hora_inicio);
    $max = strtotime('2019-01-01 ' . $hora_termino);

    $mCalc = self::min_max_numero($min, $max);

    $res = date("H:i:s",$mCalc);

    return $res;
}  


public static function count_decimals($x) {
   return strlen(substr(strrchr($x+"", "."), 1));
}

/* ej:
var_dump(random(0.001, 0.009)); // 0.004
var_dump(random(0.001, 0.009, 1)); // 0.0046
var_dump(random(0.001, 0.009, 2)); // 0.00458
var_dump(random(0.001, 0.009, 5)); // 0.00458014
*/
public static function decimal_random($min, $max, $precision = 0) {
   $decimals = max(self::count_decimals($min), self::count_decimals($max)) + $precision;
   $factor = pow(10, $decimals);
   return rand($min*$factor, $max*$factor) / $factor;
}

/*ej:
$lista = array();
array_push($lista, 14);
array_push($lista, 16);
array_push($lista, 18);
array_push($lista, 20);
array_push($lista, 22);
array_push($lista, 29);

$prob = util::elegir_elemento($lista);
*/
public static function elegir_elemento($lista) {

    $cant_elem = count($lista);

    $indx = self::min_max_numero(0, $cant_elem - 1);

    return $lista[$indx];
}

public static function eliminar_directorio_completo($dir, $remove = false) {
    
     $structure = glob(rtrim($dir, "/").'/*');

     if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file))
                self::eliminar_directorio_completo($file,true);
            else if(is_file($file))
                unlink($file);
        }
     }

     if($remove) rmdir($dir);
}

public static function obtenerNombreArchivoSinExtension($nombreCompleto) {
    // Usamos la función pathinfo para obtener información sobre el archivo
    $infoArchivo = pathinfo($nombreCompleto);

    // Extraemos el nombre del archivo sin la extensión
    $nombreSinExtension = $infoArchivo['filename'];

    return $nombreSinExtension;
}


public static function convertirCadenaAArray($cadena) {

    $arrayResultante = explode(';', $cadena);
    return $arrayResultante;
}

public static function truncarFrase($frase, $maxCaracteres = 160) {
    // Verificar si la longitud de la frase es menor o igual al límite
    if (strlen($frase) <= $maxCaracteres) {
        return $frase;
    }

    // Truncar la frase a la longitud máxima
    $fraseRecortada = substr($frase, 0, $maxCaracteres);

    // Encontrar la última palabra completa
    $ultimaPalabra = strrpos($fraseRecortada, ' ');

    // Verificar si se encontró una última palabra completa
    if ($ultimaPalabra !== false) {
        $fraseRecortada = substr($fraseRecortada, 0, $ultimaPalabra);
    }

    return $fraseRecortada;
}


public static function flor($cantidad) {

    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890";
    $validCharNumber = strlen($validCharacters);
    $result = "";

    for ($i = 0; $i < $cantidad; $i++) {

        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];

    }
    return $result;
}

public static function texto_random($cantidad) {

    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
    $validCharNumber = strlen($validCharacters);
    $result = "";

    for ($i = 0; $i < $cantidad; $i++) {

        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];

    }
    return $result;
}

public static function numero_random($cantidad) {

    $validCharacters = "1234567890";
    $validCharNumber = strlen($validCharacters);
    $result = "";

    for ($i = 0; $i < $cantidad; $i++) {

        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];

    }
    return $result;
}

public static function max_numero($cantidad, $maximo, $cero = 0) {
    
    if($cero) {
        $num_tempo = self::numero_random($cantidad);
    } else {
        $num_tempo = self::numero_random($cantidad);
        while ($num_tempo == 0) {
            $num_tempo = self::numero_random($cantidad);
        }
    }

    if($num_tempo > $maximo) {        
            return self::max_numero($cantidad, $maximo);
    } else {       
            return $num_tempo;
    }

}

public static function min_max_numero($minimo, $maximo) { 

    return mt_rand($minimo, $maximo);
}    

public static function si_no() {
    $numero = (int)self::numero_random(2);
    $res = 0;

    if($numero%2) {
        $res = 1;
    } else {
        $res = 0;
    }

    return $res;
}

//+++++++++++++++
//+  FIN CLASS  +
//+++++++++++++++
}

