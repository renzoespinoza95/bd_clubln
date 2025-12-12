<?php
/* -------------------------------
 * Vista /tab3
 * ------------------------------- */
Flight::route('GET /tab3', function () {
    include DEFINITION;
    login_admin::autentificar_administrador();
    global $path_public;                          // asegúrate de tener esta var en tu bootstrap
    include $path_public . '/admin/mud_app/tab3.php';
});

