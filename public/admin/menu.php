<?php
// dd("menu", menu::lista_menu_con_submenus());
// print_r(menu::lista_menu_con_submenus());
// dd("info_admin", $info_admin);
$administrador_id = $info_admin['administrador_id'];
?>
<div class="container">
    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>
    <a class="brand" href="<?php echo $apphost ?>/admin/dash">
        <img src="<?php echo $varhost ?>/public/ico/logo-admin.png" alt="logo-admin" />
    </a>
    <div class="btn-group pull-right">
        <a href="#" data-toggle="dropdown" class="btn dropdown-toggle">
            <i class="icon-user"></i> 
            <?php echo $info_admin['nombres_apellidos']; ?>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a rel="facebox" href="<?php echo $apphost . "/editarAdministrador/" . $administrador_id ?>">Mis datos</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo $apphost . "/finAdmin"; ?>">Salir</a></li>
        </ul>
    </div>

<div class="nav-collapse">
    <ul class="nav">
        <?php 
        // Obtener los menús con sus submenús
        $menus = menu::lista_menu_con_submenus_por_tipo_administrador_id(
            $info_admin['tipo_administrador_id']); 
        
        foreach ($menus as $menu) { ?>
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <?php echo util::mostrar_palabra_latina($menu['titulo']); ?>
                    <b class="caret"></b>
                </a>

                <?php if (!empty($menu['lista_submenu'])) { ?>
                    <ul class="dropdown-menu">
                        <?php foreach ($menu['lista_submenu'] as $submenu) { ?>
                            <li>
                                <a href="<?php echo $apphost . $submenu['url']; ?>">
                                    <?php echo util::mostrar_palabra_latina($submenu['titulo']); ?>
                                </a>
                            </li> 
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

</div>
