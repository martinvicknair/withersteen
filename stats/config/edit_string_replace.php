<?php @session_start(); if ( $_SESSION [ 'hidden_func' ] != md5_file ( 'config.php' ) ) { $error_path = '../'; include ( '../func/func_error.php' ); exit; }
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     5.0                                                      #
# File-Release-Date:  18/05/14                                                 #
# Official web site and latest version:    http://www.php-web-statistik.de     #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2018 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
include ( 'config.php' ); // include path to config
include ( '../'.substr ( $language , 0 , strpos ( $language , "." ) )."_admin.php" ); // include language vars
//------------------------------------------------------------------------------
if ( $error_reporting == 0 ) { error_reporting(0); }
//------------------------------------------------------------------------------
if ( isset ( $_POST [ 'save' ] ) )
 {
  $content = trim ( $_POST [ 'content' ] );
  $pattern_string_replace = fopen ( "pattern_string_replace.inc" , "r+" );
  flock ( $pattern_string_replace , LOCK_EX );
   ftruncate ( $pattern_string_replace , 0 );
   fwrite ( $pattern_string_replace , $content );
  flock ( $pattern_string_replace , LOCK_UN );
  fclose ( $pattern_string_replace );
 }
//------------------------------------------------------------------------------
include ( '../func/html_header.php' ); // include html footer
//------------------------------------------------------------------------------
echo '
<table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0">
<tr>
 <td align="center" bgcolor="#D7E2EA" style="padding:10px; color:#000000; border-bottom:1px solid #0D638A;">
 <form style="margin:0px;" name="edit" action="'.$_SERVER [ 'PHP_SELF' ].'" method="post">
 <div style="padding-bottom:10px;">'.$lang_admin_re[4].'</div>
 <textarea name="content" rows="15" style="width:100%; height:380px; background-color:#F9FAFB;">
';
readfile ( "pattern_string_replace.inc" );
 echo '</textarea>
 </td>
</tr>
<tr>
 <td class="th2 center" style="height:24px; padding:2px;">
 <input type="hidden" name="save" value="1">
 <input type="submit" class="submit"  value="'.$lang_admin_f[1].'">
 </form>
 </td>
</tr>
</table>
';
//------------------------------------------------------------------------------
include ( '../func/html_footer.php' ); // include html footer
//------------------------------------------------------------------------------
?>