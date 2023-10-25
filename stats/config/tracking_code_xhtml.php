<?php
//------------------------------------------------------------------------------
if ( strpos ( strtolower ( $_SERVER [ "PHP_SELF" ] ) , "tracking_code_xhtml.php" ) > 0 )
 {
  exit;
 }
else
 {
  echo '
  <!-- PHP Web Stat -->
  <script type="text/javascript" src="https://withersteen.zapto.org/stats/pws.php?mode=js"></script>
  <!-- End PHP Web Stat Code -->
  ';
 }
//------------------------------------------------------------------------------
?>