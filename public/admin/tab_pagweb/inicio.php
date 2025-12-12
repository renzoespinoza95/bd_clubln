<?php include $path_public . "/admin/header.php"; ?>
<body>
<?php
boot::select2();
boot::vuejs2();
boot::datatables();
boot::apprise();
boot::summernote();
?>
  <!-- fin sidebar -->
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
<?php include $path_public . "/admin/menu.php"; ?>
    </div>
  </div>
  <div class="container">
  <?php 
  include "vista.php"; 
  include $path_public . "/admin/footer.php"; 
  ?>
  </div>
</body>
</html>
