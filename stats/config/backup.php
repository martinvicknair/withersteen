<?php @session_start(); if ( $_SESSION [ 'hidden_func' ] != md5_file ( 'config.php' ) ) { $error_path = '../'; include ( '../func/func_error.php' ); exit; }
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     11.0                                                     #
# File-Release-Date:  22/09/26                                                 #
# Official web site and latest version:    http://www.php-web-statistik.de     #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2022 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
include ( 'config.php' ); // include path to style
include ( '../'.substr ( $language , 0 , strpos ( $language , "." ) )."_admin.php" ); // include language vars
//------------------------------------------------------------------------------
if ( $language == "language/german.php"     ) { $lang = "de";    }
if ( $language == "language/english.php"    ) { $lang = "en";    }
if ( $language == "language/dutch.php"      ) { $lang = "nl";    }
if ( $language == "language/italian.php"    ) { $lang = "it";    }
if ( $language == "language/spanish.php"    ) { $lang = "es";    }
if ( $language == "language/danish.php"     ) { $lang = "dk";    }
if ( $language == "language/french.php"     ) { $lang = "fr";    }
if ( $language == "language/turkish.php"    ) { $lang = "tr";    }
if ( $language == "language/portuguese.php" ) { $lang = "pt";    }
if ( $language == "language/finnish.php"    ) { $lang = "fi";    }
//------------------------------------------------------------------------------
if ( isset ( $_GET [ 'parameter' ] ) && $_GET [ 'parameter' ] == 'reload' )
 {
  echo '<script language="javaScript"> top.location.replace(\'admin.php?action=backup&lang='.$lang.'\'); </script>';
  exit;
 }
//------------------------------------------------------------------------------
include ( '../func/html_header.php' ); // include html header
//------------------------------------------------------------------------------
if ( ( !isset ( $_POST [ 'backup' ] ) ) && ( !isset ( $_GET [ 'backup' ] ) ) )
 {
  echo '
  <form style="margin:0px" action="'.$_SERVER [ 'PHP_SELF' ].'" method="post">
    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2" class="th2 bold center" style="height:18px; padding:2px; border-bottom:1px solid #0D638A;">'; if ( $db_active == 1 ) { echo ''.$lang_admin_cfb[1].''; } else { echo ''.$lang_admin_lfb[1].''; } echo '</td>
    </tr>
    <tr>
      <td class="bg2">'; if ( $db_active == 1 ) { echo ''.$lang_admin_cfb[2].''; } else { echo ''.$lang_admin_lfb[2].''; } echo '<br><div class="small">'.$lang_admin_cb[3].'<br><br>'.$lang_admin_cb[4].'</div></td>
      <td class="bg3">
      <input type="radio" name="backup" value="zip" checked>ZIP
      <input type="radio" name="backup" value="copy">COPY
      </td>
    </tr>
    <tr>
      <td colspan="2" class="th2 center" style="height:24px; padding:2px; border-top:1px solid #0D638A;">
      <input type="submit" style="border:1px solid #7F9DB9; color:#000000; font-family:Verdana,Arial,Sans-Serif; font-size:12px; background-color:#FEFEFE; padding: 1px 10px 1px 10px; width:auto; overflow:visible; cursor:pointer;" value="'.$lang_admin_cb[5].'">
      </td>
    </tr>
    </table>
  </form>
  ';
 }
else
 {
  //----------------------------------------------------------------------------
  ini_set ( "memory_limit" , "200M" );
  //----------------------------------------------------------------------------
  if ( isset ( $_POST [ 'backup' ] ) && $_POST [ 'backup' ] == 'zip' )
   {
    echo '<img src="../images/loading_indicator_48.gif" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%)" alt="">';
	echo '<meta http-equiv="refresh" content="1; URL=backup.php?create=zip&backup=1">';
    exit;
   }
  //----------------------------------------------------------------------------
  if ( isset ( $_GET [ 'create' ] ) && $_GET [ 'create' ] == 'zip' )
   {
    //------------------------------------
    $configdir = '';
    $logdir    = '../log/';
    $filename  = "../backup/backup_".date ( "Y-m-d" ).".zip";
    //------------------------------------
    $zip = new ZipArchive();
    $zip->open($filename , ZipArchive::CREATE);
	//------------------------------------
    if ( $db_active == 1 )
     {
      $zip->addFile($configdir."pattern_site_name.inc"      , "config/pattern_site_name.inc");
      $zip->addFile($configdir."pattern_string_replace.inc" , "config/pattern_string_replace.inc");
      $zip->addFile($logdir."cache_time_stamp.php"          , "log/cache_time_stamp.php");
      $zip->addFile($logdir."cache_time_stamp_archive.php"  , "log/cache_time_stamp_archive.php");
      $zip->addFile($logdir."cache_visitors.php"            , "log/cache_visitors.php");
      $zip->addFile($logdir."cache_visitors_archive.php"    , "log/cache_visitors_archive.php");
      $zip->addFile($logdir."cache_visitors_counter.php"    , "log/cache_visitors_counter.php");
      $zip->addFile($logdir."last_logins.dta"               , "log/last_logins.dta");
      $zip->addFile($logdir."last_timestamp.dta"            , "log/last_timestamp.dta");
      $zip->addFile($logdir."timestamp_cache_update.dta"    , "log/timestamp_cache_update.dta");
     }
    else
     {
      $zip->addFile($configdir."pattern_site_name.inc"      , "config/pattern_site_name.inc");
      $zip->addFile($configdir."pattern_string_replace.inc" , "config/pattern_string_replace.inc");
      $zip->addFile($logdir."cache_time_stamp.php"          , "log/cache_time_stamp.php");
      $zip->addFile($logdir."cache_time_stamp_archive.php"  , "log/cache_time_stamp_archive.php");
      $zip->addFile($logdir."cache_visitors.php"            , "log/cache_visitors.php");
      $zip->addFile($logdir."cache_visitors_archive.php"    , "log/cache_visitors_archive.php");
      $zip->addFile($logdir."cache_visitors_counter.php"    , "log/cache_visitors_counter.php");
      $zip->addFile($logdir."index_days.php"                , "log/index_days.php");
      $zip->addFile($logdir."last_logins.dta"               , "log/last_logins.dta");
      $zip->addFile($logdir."last_timestamp.dta"            , "log/last_timestamp.dta");
      $zip->addFile($logdir."logdb.dta"                     , "log/logdb.dta");
      $zip->addFile($logdir."logdb_backup.dta"              , "log/logdb_backup.dta");
      $zip->addFile($logdir."pattern_browser.dta"           , "log/pattern_browser.dta");
      $zip->addFile($logdir."pattern_operating_system.dta"  , "log/pattern_operating_system.dta");
      $zip->addFile($logdir."pattern_referer.dta"           , "log/pattern_referer.dta");
      $zip->addFile($logdir."pattern_resolution.dta"        , "log/pattern_resolution.dta");
      $zip->addFile($logdir."pattern_site_name.dta"         , "log/pattern_site_name.dta");
      $zip->addFile($logdir."timestamp_cache_update.dta"    , "log/timestamp_cache_update.dta");
     }
    $zip->close();
    //------------------------------------
    echo '<div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%)"><img src="../images/admin/done.png" style="border:0; width:32px; height:32px; vertical-align:middle" alt=""> &nbsp; <b>'.$lang_admin_cb[6].'</b></div>';
	echo '<meta http-equiv="refresh" content="2; URL=backup.php?parameter=reload">';
    //------------------------------------
   }
  else
   {
    //------------------------------------
    $configdir = '';
    $logdir    = '../log/';
    //------------------------------------
    @mkdir ( "../backup/backup_".date ( "Y-m-d" ) );
    @mkdir ( "../backup/backup_".date ( "Y-m-d" )."/config" );
    @mkdir ( "../backup/backup_".date ( "Y-m-d" )."/log" );
    //------------------------------------
    $backupconfigdir = "../backup/backup_".date ( "Y-m-d" )."/config/";
    $backuplogdir    = "../backup/backup_".date ( "Y-m-d" )."/log/";
    //------------------------------------
    if ( $db_active == 1 )
     {
      copy ($configdir."pattern_site_name.inc"      , $backupconfigdir."pattern_site_name.inc");
      copy ($configdir."pattern_string_replace.inc" , $backupconfigdir."pattern_string_replace.inc");
      copy ($logdir."cache_time_stamp.php"          , $backuplogdir."cache_time_stamp.php");
      copy ($logdir."cache_time_stamp_archive.php"  , $backuplogdir."cache_time_stamp_archive.php");
      copy ($logdir."cache_visitors.php"            , $backuplogdir."cache_visitors.php");
      copy ($logdir."cache_visitors_archive.php"    , $backuplogdir."cache_visitors_archive.php");
      copy ($logdir."cache_visitors_counter.php"    , $backuplogdir."cache_visitors_counter.php");
      copy ($logdir."last_logins.dta"               , $backuplogdir."last_logins.dta");
      copy ($logdir."last_timestamp.dta"            , $backuplogdir."last_timestamp.dta");
      copy ($logdir."timestamp_cache_update.dta"    , $backuplogdir."timestamp_cache_update.dta");
     }
    else
     {
      copy ($configdir."pattern_site_name.inc"      , $backupconfigdir."pattern_site_name.inc");
      copy ($configdir."pattern_string_replace.inc" , $backupconfigdir."pattern_string_replace.inc");
      copy ($logdir."cache_time_stamp.php"          , $backuplogdir."cache_time_stamp.php");
      copy ($logdir."cache_time_stamp_archive.php"  , $backuplogdir."cache_time_stamp_archive.php");
      copy ($logdir."cache_visitors.php"            , $backuplogdir."cache_visitors.php");
      copy ($logdir."cache_visitors_archive.php"    , $backuplogdir."cache_visitors_archive.php");
      copy ($logdir."cache_visitors_counter.php"    , $backuplogdir."cache_visitors_counter.php");
      copy ($logdir."index_days.php"                , $backuplogdir."index_days.php");
      copy ($logdir."last_logins.dta"               , $backuplogdir."last_logins.dta");
      copy ($logdir."last_timestamp.dta"            , $backuplogdir."last_timestamp.dta");
      copy ($logdir."logdb.dta"                     , $backuplogdir."logdb.dta");
      copy ($logdir."logdb_backup.dta"              , $backuplogdir."logdb_backup.dta");
      copy ($logdir."pattern_browser.dta"           , $backuplogdir."pattern_browser.dta");
      copy ($logdir."pattern_operating_system.dta"  , $backuplogdir."pattern_operating_system.dta");
      copy ($logdir."pattern_referer.dta"           , $backuplogdir."pattern_referer.dta");
      copy ($logdir."pattern_resolution.dta"        , $backuplogdir."pattern_resolution.dta");
      copy ($logdir."pattern_site_name.dta"         , $backuplogdir."pattern_site_name.dta");
      copy ($logdir."timestamp_cache_update.dta"    , $backuplogdir."timestamp_cache_update.dta");
     }
    //------------------------------------
    echo '<div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%)"><img src="../images/admin/done.png" style="border:0; width:32px; height:32px; vertical-align:middle" alt=""> &nbsp; <b>'.$lang_admin_cb[6].'</b></div>';
    echo '<meta http-equiv="refresh" content="2; URL=backup.php?parameter=reload">';
    //------------------------------------
   }
 }
//------------------------------------------------------------------------------
include ( '../func/html_footer.php' ); // include html footer
//------------------------------------------------------------------------------
?>