<?php
//------------------------------------------------------------------------------
if ( strpos ( strtolower ( $_SERVER [ "PHP_SELF" ] ) , "tracking_code.php" ) > 0 )
 {
  exit;
 }
else
 {
  echo '
  <!-- PHP Web Stat -->
  <script src="https://withersteen.zapto.org/stats/pws.php?mode=js"></script>
  <!-- End PHP Web Stat Code -->
  ';
 }
//------------------------------------------------------------------------------
?>