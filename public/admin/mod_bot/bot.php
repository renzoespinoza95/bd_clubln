<?php
Flight::route('GET /tt/phpi', function () {
    phpinfo();
});

Flight::route('GET /flask', function () {
    echo util::ok();
});


Flight::route('GET /tt/tt', function () {
    include DEFINITION;
    echo util::mi_barsi("hola");
});

Flight::route('GET /tt/diario', function () {
    include DEFINITION;

    $usu_id = "3";
    $fich_id_dest = "6";

    //diario(1, 'inicio_conversacion', ['fich_id_dest' => 8]);

    //diario($usu_id, 'inicio_sesion', null);

    diario($usu_id, 'nuevo_miembro', null);    

    echo poke();
});
