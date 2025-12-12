<li class="dropdown-submenu">
    <a tabindex="-1" href="#">
<?php
echo $menu_padre['titulo'];
?>
    </a>
   <ul class="dropdown-menu">    
<?php
foreach(submenu::lista_submenu(" WHERE menu.menu_id =" . $menu_padre['menu_id']) as $submenu) {
?>
  <li>
      <a href="<?php echo $apphost . $submenu['url']; ?>">
      <?php echo util::mostrar_palabra_latina($submenu['titulo']) ?>
      </a>
  </li> 
<?php
}
?>
    </ul>
 </li>