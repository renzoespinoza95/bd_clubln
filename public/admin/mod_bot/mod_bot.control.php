<?php
Flight::route('GET /testi/llenarPublicaciones', function () {

    $eventos = DB::query("SELECT id, titulo, url FROM eventos");

    $actualizados = 0;
    foreach ($eventos as $evento) {
        $id = $evento['id'];
        $titulo = $evento['titulo'];
        $url = $evento['url'];

        if (is_null($url) || trim($url) === '') {
            $nueva_url = util::url_amigable($titulo);
            DB::update('eventos', ['url' => $nueva_url], "id=%i", $id);
            $actualizados++;
        }
    }

    //Flight::json(['status' => 'ok', 'actualizados' => $actualizados]);
    echo poke();
});

