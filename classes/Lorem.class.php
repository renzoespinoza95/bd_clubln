<?php
abstract class Lorem {
    public static function ipsum($cant_letras) {
        
        return implode  (" ", self::random_values(self::$lorem, $cant_letras));

    }

    public static function random_float() {
        return (int)util::numero_random(3);
    }

    public static function random_values($arr, $cant_palabras = 10) {
        
        if($cant_palabras == 1 || $cant_palabras == 0) {
            $cant_palabras = 2;
        }
        $seleccionados = array();

        $keys = array_rand($arr, $cant_palabras);

        return array_intersect_key($arr, array_fill_keys($keys, null));
    }

    // total = 180 elementos
    private static $lorem = array('lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'praesent', 'interdum', 'dictum', 'mi', 'non', 'egestas', 'nulla', 'in', 'lacus', 'sed', 'sapien', 'placerat', 'malesuada', 'at', 'erat', 'etiam', 'josefina', 'velit', 'finibus', 'viverra', 'maecenas', 'mattis', 'volutpat', 'justus', 'vitae', 'vestibulum', 'metus', 'lobortis', 'mauris', 'luctus', 'leo', 'feugiat', 'nibh', 'tincidunt', 'a', 'integer', 'facilisis', 'lacinia', 'ligula', 'ac', 'suspendisse', 'eleifend', 'nunc', 'nec', 'pulvinar', 'quisque', 'ut', 'semper', 'auctor', 'tortor', 'mollis', 'est', 'tempor', 'scelerisque', 'venenatis', 'quis', 'ultrices', 'tellus', 'nisi', 'phasellus', 'aliquam', 'molestie', 'purus', 'convallis', 'cursus', 'ex', 'massa', 'fusce', 'felis', 'fringilla', 'faucibus', 'varius', 'ante', 'primis', 'orci', 'et', 'posuere', 'cubilia', 'curae', 'proin', 'ultricies', 'hendrerit', 'ornare', 'augue', 'pharetra', 'dapibus', 'nullam', 'sollicitudin', 'euismod', 'eget', 'pretium', 'vulputate', 'urna', 'arcu', 'porttitor', 'quam', 'condimentum', 'consequat', 'tempus', 'hac', 'habitasse', 'platea', 'dictumst', 'sagittis', 'helena', 'gravida', 'eu', 'commodo', 'dui', 'lectus', 'vivamus', 'libero', 'vel', 'maximus', 'pellentesque', 'efficitur', 'pletora', 'aptent', 'taciti', 'sociosqu', 'amarilis', 'litora', 'torquent', 'bianca', 'conubia', 'nostra', 'inceptos', 'himenaeos', 'fermentum', 'turpis', 'donec', 'magna', 'porta', 'enim', 'curabitur', 'odio', 'rhoncus', 'blandit', 'potenti', 'sodales', 'accumsan', 'congue', 'neque', 'duis', 'bibendum', 'laoreet', 'elementum', 'suscipit', 'diam', 'vehicula', 'eros', 'nam', 'imperdiet', 'sem', 'ullamcorper', 'dignissim', 'risus', 'aliquet', 'habitant', 'morbi', 'tristique', 'senectus', 'netus', 'females', 'nisl', 'iaculis', 'cras', 'Árbol', 'Césped', 'Cáctus', 'Biósfera', 'Clorofila', 'Río', 'Orquídea', 'Ciénaga', 'Lúcuma', 'Cañón', 'Páramo', 'Móvil', 'Níquel', 'Ébano', 'Látigo', 'Nébula', 'Hábito', 'Níspero', 'Cóndor', 'Lémur', 'Trópico', 'Águila', 'Cúrcuma', 'Oxígeno', 'Índigo', 'Húmedo', 'Cálido', 'Úrsido', 'Músculo', 'Selénico', 'Gélido', 'Ráfaga', 'Néctar', 'Trébol', 'Sépalos', 'Gérmenes', 'Nácar', 'Túmulo', 'Máguey', 'Química', 'Túnel', 'Relámpago', 'Sótano', 'Sábanas', 'Mímesis', 'Vórtice', 'Hídrico', 'Dócil', 'Víbora', 'Múrice', 'Ciénaga', 'Lágrima', 'Enérgico', 'Biótico', 'Hórrido', 'Sólido', 'Líquido', 'Ámbar', 'Mármol', 'Cúmulo', 'Estrépito', 'Óxido', 'Súbitas', 'Súperficie', 'Ántrax', 'Súmmum', 'Súber', 'Rótula', 'Réptil', 'Fértil', 'Ópalo', 'Útil', 'Sésiles', 'Pálido', 'Ígneo', 'Óxido', 'Ímpetu', 'Tórrido', 'Zócalo', 'Zángano', 'Pólen', 'Ráfaga', 'Hálito', 'Hígado', 'Mórbido', 'Mística', 'Órdenes', 'Áureo', 'Estéreo', 'Lóbulo', 'Ínsula', 'Vórtices', 'Vértebra', 'Cáustico', 'Mórula', 'Híbrido', 'Úrsido', 'Térmico', 'Cíclico', 'Dátil');
}