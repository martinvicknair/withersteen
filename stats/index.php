<?php @session_start();
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     20.01                                                    #
# File-Release-Date:  23/08/26                                                 #
# Official web site and latest version:    https://www.php-web-statistik.de    #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################
/*
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this
program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth
Floor, Boston, MA 02110, USA
*/

//------------------------------------------------------------------------------
// @set_time_limit ( 0 );
@ini_set ( 'max_execution_time', 'false' ); // set the script time
//------------------------------------------------------------------------------
##### !!! never change this value !!! #####
$version_number  = '20';
$revision_number = '.01';
$last_edit       = '2023';
//------------------------------------------------------------------------------
// set opcache to disabled
@ini_set ( 'opcache.enable', 0 );
//------------------------------------------------------------------------------
// logout
if ( isset ( $_GET [ 'parameter' ] ) && ( $_GET [ 'parameter' ] == 'logout' ) ) { session_destroy(); session_start(); }
//------------------------------------------------------------------------------
include ( 'config/config.php'                ); // include path to logfile
include ( $language                          ); // include language vars
include ( $language_patterns                 ); // include language country vars
//------------------------------------------------------------------------------
if ( $error_reporting == 0 ) { error_reporting(0); }
//------------------------------------------------------------------------------
if ( $db_active == 1 )
 {
  include ( 'config/config_db.php'           ); // include db prefix
  include ( 'func/func_db_connect.php'       ); // include database connection
 }
//------------------------------------------------------------------------------
if ( ( $script_domain == "http://www.example.com" ) && ( $adminpassword == "admin" ) )
 {
  echo '<meta http-equiv="refresh" content="0; url=config/setup.php">';
  exit;
 }
//------------------------------------------------------------------------------
include ( 'func/func_pattern_reverse.php'    ); // include pattern reverse function
include ( 'func/func_pattern_matching.php'   ); // include pattern matching function
include ( 'func/func_pattern_icons.php'      ); // include pattern icons function
include ( 'func/func_kill_special_chars.php' ); // include umlaut function
include ( 'func/func_file_row_size_big.php'  ); // include count file lines
include ( 'func/func_display.php'            ); // include display function
include ( 'func/func_read_dir.php'           ); // include read directory function
include ( 'func/func_timer.php'              ); // include stopwatch function
include ( 'func/func_last_logins.php'        ); // include last login log function
include ( 'func/func_crypt.php'              ); // include password comparison function
include ( 'func/func_load_plugins.php'       ); // include plugins
//------------------------------------------------------------------------------
// check date form
    if ( $language == 'language/german.php' ) { $dateform = 'd.m.Y'; $dateform1 = 'd.m.y'; }
elseif ( $language == 'language/french.php' ) { $dateform = 'd.m.Y'; $dateform1 = 'd.m.y'; }
  else { $dateform = 'Y/m/d'; $dateform1 = 'y/m/d'; }
//------------------------------------------------------------------------------
// check the loggedin session
if ( isset ( $_POST [ 'password' ] ) )
 {
  // check for admin session
  if ( strpos ( $adminpassword , 'Pass_' ) !== FALSE ) { if ( passCrypt ( $_POST [ 'password' ] ) == $adminpassword ) { $_SESSION [ 'loggedin' ] = 'admin'; } }
  else { if ( md5 ( $_POST [ 'password' ] ) == md5 ( $adminpassword ) ) { $_SESSION [ 'loggedin' ] = 'admin'; } } // old plain text saved passwords
  // check for client session
  if ( strpos ( $clientpassword , 'Pass_' ) !== FALSE ) { if ( passCrypt ( $_POST [ 'password' ] ) == $clientpassword ) { $_SESSION [ 'loggedin' ] = 'client'; } }
  else { if ( md5 ( $_POST [ 'password' ] ) == md5 ( $clientpassword ) ) { $_SESSION [ 'loggedin' ] = 'client'; } } // old plain text saved passwords
 }
else
 {
  // set the loggedin session to user
  if ( !isset ( $_SESSION [ 'loggedin' ] ) ) { $_SESSION [ 'loggedin' ] = "user"; }
 }
//------------------------------------------------------------------------------
if ( isset ( $_GET [ 'parameter' ] ) && ( $_GET [ 'parameter' ] == 'autologout' ) )
 {
  // autologout
  echo '<!DOCTYPE html>
  <html lang="en">
  <head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>PHP Web Stat '.$version_number.$revision_number.'</title>
   <meta name="title" content="PHP Web Stat '.$version_number.$revision_number.'">
   <link rel="stylesheet" type="text/css" href="css/style.css?ver='.time().'">
   <link rel="stylesheet" type="text/css" href="'.$theme.'style.css?ver='.time().'">
   <link rel="shortcut icon" href="images/favicon.ico">
   <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
   <!--[if lt IE 9]>
     <script src="js/html5shiv.js"></script>
   <![endif]-->
  </head>
  <body onload="document.login.password.focus(); document.login.password.select();">
  <div id="autologout">
    <div class="brand clearfix" style="position:relative; left:50%; transform:translateX(-50%); margin:25px 0 20px">
      <a href="https://www.php-web-statistik.de" target="_blank" style="float:left; margin-right:15px"><img src="images/system.png" style="height:50px; width:auto" alt="PHP Web Stat" title="PHP Web Stat"></a>
      <div class="brand-inline">
        <div class="brand-name">PHP Web Stat</div>
        <div class="brand-plus">fast, simple & smart</div>
      </div>
    </div>
    <div class="info">'.$lang_autologout[1].'</div>
    <div class="data-input">
      <p style="margin-top:0; margin-bottom:8px">'.$lang_autologout[2].'</p>
      <form name="login" action="index.php" method="post">
      <div class="form-group">
        <label class="sr-only" for="password">'.$lang_login[3].'</label>
        <div class="input-group">
          <div class="input-group-addon"><span class="glyphicon glyphicon-lock fa-lg"></span></div>
          <input type="password" name="password" id="password" class="form-control" placeholder="'.$lang_login[3].'">
        </div>
      </div>
      <button type="submit" class="btn btn-sm" style="float:right"><span class="glyphicon glyphicon-log-in"></span> '.$lang_login[4].'</button>
      </form>
    </div>
    <div class="footer">
      Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="new">PHP Web Stat</a> &nbsp;<b>&middot;</b>&nbsp; Version '.$version_number.$revision_number.'
    </div>
  </div>';
  include ( "func/html_footer.php" ); // include html footer
  session_unset();
  session_destroy();
  exit;
 }
//------------------------------------------------------------------------------
if ( !isset ( $_SESSION [ 'hidden_stat' ] ) ) { $_SESSION [ 'hidden_stat' ] = null; }

if ( ( $loginpassword_ask == 1 ) && ( $_SESSION [ 'hidden_stat' ] != md5_file ( 'config/config.php' ) ) )
 {
  if ( ( $clientpassword == "" ) || ( $clientpassword == "Pass_ZGEzOWEzZWU1ZTZiNGIwZDMyN" ) )
   {
    for ( $i = 0; $i < 20; $i++ ) { $clientpassword = $clientpassword . chr( rand( 33,90 ) ); }
   }
  if ( ( !isset ( $_POST [ 'password' ] ) ) || ( ( passCrypt ( $_POST [ 'password' ] ) != $adminpassword ) && ( md5 ( $_POST [ 'password' ] ) != md5 ( $adminpassword ) ) && ( passCrypt ( $_POST [ 'password' ] ) != $clientpassword ) && ( md5 ( $_POST [ 'password' ] ) != md5 ( $clientpassword ) ) ) )
   {

    // login
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>PHP Web Stat '.$version_number.$revision_number.'</title>
     <meta name="title" content="PHP Web Stat '.$version_number.$revision_number.'">
     <link rel="stylesheet" type="text/css" href="css/style.css?ver='.time().'">
     <link rel="stylesheet" type="text/css" href="'.$theme.'style.css?ver='.time().'">
     <link rel="shortcut icon" href="images/favicon.ico">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
     <!--[if lt IE 9]>
       <script src="js/html5shiv.js"></script>
     <![endif]-->
    </head>
    <body onload="document.login.password.focus(); document.login.password.select();">
    <div id="login">
      <img src="images/loading_indicator_48.gif" style="width:1px; height:1px; display:none" alt="">
      <div class="brand clearfix" style="position:relative; left:50%; transform:translateX(-50%); margin:25px 0 20px">
        <a href="https://www.php-web-statistik.de" target="_blank" style="float:left; margin-right:15px"><img src="images/system.png" style="height:50px; width:auto" alt="PHP Web Stat" title="PHP Web Stat"></a>
        <div class="brand-inline">
          <div class="brand-name">PHP Web Stat</div>
          <div class="brand-plus">fast, simple & smart</div>
        </div>
      </div>
      <div class="info">'.$lang_login[1].'</div>
      <div class="data-input">
        <p style="margin-top:0; margin-bottom:8px">'.$lang_login[2].'</p>
        <form name="login" action="index.php" method="post">
        <div class="form-group">
          <label class="sr-only" for="password">'.$lang_login[3].'</label>
          <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-lock fa-lg"></span></div>
            <input type="password" name="password" id="password" class="form-control" placeholder="'.$lang_login[3].'">
          </div>
        </div>
        <button type="submit" class="btn btn-sm" style="float:right"><span class="glyphicon glyphicon-log-in"></span> '.$lang_login[4].'</button>
        </form>
      </div>
      <div class="footer">
        Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="new">PHP Web Stat</a> &nbsp;<b>&middot;</b>&nbsp; Version '.$version_number.$revision_number.'
      </div>
    </div>';
    include ( "func/html_footer.php" ); // include html footer
    exit;
   }
 }
//------------------------------------------------------------------------------
$_SESSION [ 'hidden_stat' ] = md5_file ( 'config/config.php' );
//------------------------------------------------------------------------------
// log login
if ( ( !isset ( $_GET [ 'parameter' ] ) || $_GET [ 'parameter' ] != 'finished' ) && ( !isset ( $_GET [ 'action' ] ) || $_GET [ 'action' ] != 'backtostat' ) && ( !isset ( $_POST [ 'archive' ] ) || $_POST [ 'archive' ] != '1' ) )
 {
  if     ( ( passCrypt ( $_POST [ 'password' ] ) == $adminpassword ) || ( md5 ( $_POST [ 'password' ] ) == md5 ( $adminpassword ) ) ) { last_login_log ( 'adminpassword' ); $_SESSION [ 'loggedin' ] = 'admin'; }
  elseif ( ( ( passCrypt ( $_POST [ 'password' ] ) == $clientpassword ) || ( md5 ( $_POST [ 'password' ] ) == md5 ( $clientpassword ) ) ) && ( $clientpassword != "" ) ) { last_login_log ( 'userpassword' ); $_SESSION [ 'loggedin' ] = 'client'; }
  else   { last_login_log ( "user" ); $_SESSION [ 'loggedin' ] = 'user'; }
 }
//------------------------------------------------------------------------------
// cache refresh
if ( ( !isset ( $_GET [ 'parameter' ] ) ) || ( $_GET [ 'parameter' ] != 'finished' ) )
 {
  if ( isset ( $_POST [ 'archive' ] ) )
   {
    if ( $_POST [ 'from_timestamp' ] && $_POST [ 'until_timestamp' ] )
     {
      //--------------------------------
      $time_stamp_generate = mktime ( 0 , 0 , 0 , substr ( $_POST [ 'from_timestamp' ] , 3 , 2 ) , substr ( $_POST [ 'from_timestamp' ] , 0 , 2 ) , substr ( $_POST [ 'from_timestamp' ] , 6 ) )."-".mktime ( 23 , 59 , 59 , substr ( $_POST [ 'until_timestamp' ] , 3 , 2 ) , substr ( $_POST [ 'until_timestamp' ] , 0 , 2 ) , substr ( $_POST [ 'until_timestamp' ] , 6 ) ) ;
      $load_logfile = "cache_creator.php?loadfile=2&archive=".$time_stamp_generate;
      unset ( $time_stamp_generate );
      //--------------------------------
      $cache_timestamp_file = fopen ( "log/cache_time_stamp_archive.php" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , "<?php ?>" ); // php header + footer
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //--------------------------------
      $cache_visitors_file = fopen ( "log/cache_visitors_archive.php" , "r+" );
      flock ( $cache_visitors_file , LOCK_EX );
       ftruncate ( $cache_visitors_file , 0 );
       fwrite ( $cache_visitors_file , "<?php ?>" ); // php header + footer
      flock ( $cache_visitors_file , LOCK_UN );
      fclose ( $cache_visitors_file );
      unset  ( $cache_visitors_file );
      //--------------------------------
     }
    else
     {
      //--------------------------------
      if ( ! is_numeric ( $_POST [ 'archive' ] ) ) { $_POST [ 'archive' ] = 1; }
      $load_logfile = "cache_creator.php?loadfile=2&archive=".$_POST [ 'archive' ];
      //--------------------------------
      $cache_timestamp_file = fopen ( "log/cache_time_stamp_archive.php" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , "<?php ?>" ); // php header + footer
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //--------------------------------
      $cache_visitors_file = fopen ( "log/cache_visitors_archive.php" , "r+" );
      flock ( $cache_visitors_file , LOCK_EX );
       ftruncate ( $cache_visitors_file , 0 );
       fwrite ( $cache_visitors_file , "<?php ?>" ); // php header + footer
      flock ( $cache_visitors_file , LOCK_UN );
      fclose ( $cache_visitors_file );
      unset  ( $cache_visitors_file );
      //--------------------------------
     }
   }
  else
   {
    if ( ( isset ( $_GET [ 'from_timestamp' ] ) ) && ( isset ( $_GET [ 'until_timestamp' ] ) ) )
     {
      //--------------------------------
      $time_stamp_generate = mktime ( 0 , 0 , 0 , substr ( $_GET [ 'from_timestamp' ] , 3 , 2 ) , substr ( $_GET [ 'from_timestamp' ] , 0 , 2 ) , substr ( $_GET [ 'from_timestamp' ] , 6 ) )."-".mktime ( 23 , 59 , 59 , substr ( $_GET [ 'until_timestamp' ] , 3 , 2 ) , substr ( $_GET [ 'until_timestamp' ] , 0 , 2 ) , substr ( $_GET [ 'until_timestamp' ] , 6 ) ) ;
      $load_logfile = "cache_creator.php?loadfile=2&archive=".$time_stamp_generate;
      unset ( $time_stamp_generate );
      //--------------------------------
      $cache_timestamp_file = fopen ( "log/cache_time_stamp_archive.php" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , "<?php ?>" ); // php header + footer
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //--------------------------------
      $cache_visitors_file = fopen ( "log/cache_visitors_archive.php" , "r+" );
      flock ( $cache_visitors_file , LOCK_EX );
       ftruncate ( $cache_visitors_file , 0 );
       fwrite ( $cache_visitors_file , "<?php ?>" ); // php header + footer
      flock ( $cache_visitors_file , LOCK_UN );
      fclose ( $cache_visitors_file );
      unset  ( $cache_visitors_file );
      //--------------------------------
     }
    else
     {
      //--------------------------------
      // set the physical address to zero
      $cache_timestamp_file = fopen ( "log/cache_memory_address.php" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , "<?php \$cache_memory_address = \"\";?>" ); // php header + footer
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //--------------------------------
      $load_logfile = "func/func_load_creator.php?parameter=update_stat_cache";
      //--------------------------------
     }
   }
  //------------------------------------------------------------------
  // refresh box after login
  include ( "func/html_header.php" ); // include html header
  echo '
  <div id="fade-overlay-b" class="overlay_black" style="display:block"></div>
  <div id="refresh">
    <div class="header">'.$lang_refresh[1].'</div>
    <div class="indicator"><img src="images/loading_indicator_48.gif" alt="loading" title="loading"><br><iframe name="creator" src="'.$load_logfile.'" style="width:10px; height:10px; background:transparent; border:0; overflow:hidden"></iframe></div>
    <div class="info">';
    if ( ( isset ( $_POST [ 'archive' ] ) ) || ( isset ( $_GET [ 'from_timestamp' ] ) ) )
     {
      echo $lang_archive[1].'<br>'.$lang_archive[2].'';
     }
    else
     {
      echo $lang_refresh[2].'<br>'.$lang_refresh[3].'';
     }
    echo '
    </div>
    <div class="clearfix"></div>
    <div class="c-frame">';
     if ( ( isset ( $_POST [ 'archive' ] ) ) || ( isset ( $_GET [ 'from_timestamp' ] ) ) )
      {
       echo $lang_refresh[4].'<br><iframe name="timestamp_control_archive" src="func/func_timestamp_control.php?parameter=archive" style="width:200px; height:22px; border:0; margin-top:-2px; background:transparent; overflow:hidden"></iframe>';
      }
     else
      {
       echo $lang_refresh[4].'<br><iframe name="timestamp_control_stat" src="func/func_timestamp_control.php?parameter=stat" style="width:200px; height:22px; border:0; margin-top:-2px; background:transparent; overflow:hidden"></iframe>';
      }
     echo '
    </div>
  </div>
  ';
  include ( "func/html_footer.php" ); // include html footer
  exit;
  //------------------------------------------------------------------
 }
//------------------------------------------------------------------------------
$timer_start = timer_start(); // start the stopwatch timer
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  //--------------------------------
  if ( isset ( $_GET [ 'archive' ] ) )
   {
    include ( "log/cache_time_stamp_archive.php" ); // include the last timestamp
    include ( "log/cache_visitors_archive.php"   ); // include the saved arrays
   }
  //--------------------------------
  if ( isset ( $_GET [ 'archive_save' ] ) )
   {
    if ( ( strpos ( $_GET [ 'archive_save' ]  , "log/archive/" ) != 0 ) || ( strpos( $_GET [ 'archive_save' ] , ".." ) === true) || ( !file_exists ( $_GET [ 'archive_save' ] ) ) )
     {
      exit;
     }
    else
     {
      include ( $_GET [ 'archive_save' ] ); // include the saved arrays
     }
   }
  //--------------------------------
 }
else
 {
  //--------------------------------
  include ( "log/cache_time_stamp.php" ); // include the last timestamp
  include ( "log/cache_visitors.php"   ); // include the saved arrays
  //--------------------------------
 }
//------------------------------------------------------------------------------
// take the first timestamp

// if database version
if ( $db_active == 1 )
 {
  // get the real first tracking timestamp
  $query                = "SELECT MIN(timestamp) FROM ".$db_prefix."_main";
  $result               = db_query ( $query , 1 , 0 );
  $real_first_timestamp = $result[0][0];

  if ( isset ( $starting_date ) )
   {
    if ( $starting_date == "TT.MM.YYYY" )
     {
      $query           = "SELECT MIN(timestamp) FROM ".$db_prefix."_main";
      $result          = db_query ( $query , 1 , 0 );
      $first_timestamp = date ( $dateform , $result[0][0] );
     }
    else
     {
      $first_timestamp = $starting_date;
     }
   }
  else
   {
    $query           = "SELECT MIN(timestamp) FROM ".$db_prefix."_main";
    $result          = db_query ( $query , 1 , 0 );
    $first_timestamp = date ( $dateform , $result[0][0] );
   }
 }
else
 {
  // get the real first tracking timestamp
  $logfile_first_timestamp = fopen ( "log/logdb_backup.dta" , "r" ); // open logfile
  $logfile_real_first_timestamp = fgetcsv ( $logfile_first_timestamp , 60000 , "|" );
  if ( isset ( $logfile_real_first_timestamp [ 0 ] ) )
   { $real_first_timestamp = $logfile_real_first_timestamp [ 0 ]; }
  else
   { $real_first_timestamp = 0; }

  // if the first line in the logfile is empty, we take the second line
  if ( $real_first_timestamp == 0 )
   {
    $logfile_real_first_timestamp = fgetcsv ( $logfile_first_timestamp , 60000 , "|" );
    if ( isset ( $logfile_real_first_timestamp [ 0 ] ) )
     { $real_first_timestamp = $logfile_real_first_timestamp [ 0 ]; }
    else
     { $real_first_timestamp = 0; }
   }

  fclose ( $logfile_first_timestamp ); // close logfile
  unset  ( $logfile_first_timestamp );
  //------------------------------------------------------------------
  if ( isset ( $starting_date ) )
   {
    if ( $starting_date == "TT.MM.YYYY" )
     {
      $logfile_first_timestamp = fopen ( "log/logdb_backup.dta" , "r" ); // open logfile
      $logfile_entry_first_timestamp = fgetcsv ( $logfile_first_timestamp , 60000 , "|" ); // read entry from logfile
      if ( isset ($logfile_entry_first_timestamp [ 0 ] ) )
       { $first_timestamp = date ( $dateform , $logfile_entry_first_timestamp [ 0 ] ); }
      else
       { $first_timestamp = 0; }

      fclose ( $logfile_first_timestamp       ); // close logfile
      unset  ( $logfile_first_timestamp       );
      unset  ( $logfile_entry_first_timestamp );
     }
    else
     {
      $first_timestamp = $starting_date;
     }
   }
  else
   {
    $logfile_first_timestamp = fopen ( "log/logdb_backup.dta" , "r" ); // open logfile
    $logfile_entry_first_timestamp = fgetcsv ( $logfile_first_timestamp , 60000 , "|" ); // read entry from logfile
    if ( isset ($logfile_entry_first_timestamp [ 0 ] ) )
     { $first_timestamp = date ( $dateform , $logfile_entry_first_timestamp [ 0 ] ); }
    else
     { $first_timestamp = 0; }

    fclose ( $logfile_first_timestamp       ); // close logfile
    unset  ( $logfile_first_timestamp       );
    unset  ( $logfile_entry_first_timestamp );
   }
 }
//------------------------------------------------------------------------------
// page impressions
if ( $db_active == 1 )
 {
  //------------------------------------------------------------------
  $query           = "SELECT COUNT(*) from ".$db_prefix."_main";
  $result          = db_query ( $query , 1 , 0 );
  $pageimpressions = $result[0][0];
  //------------------------------------------------------------------
 }
else
 {
  $pilogfile = "log/logdb_backup.dta";
  if ( filesize ( $pilogfile ) != 0 )
   { $pageimpressions = file_row_size_big ( $pilogfile ); }
  else
   { $pageimpressions = 0; }
 }
//------------------------------------------------------------------------------
clearstatcache(); // empty the filecache to get the real live data
//------------------------------------------------------------------------------
// check if details of every browser should be displayed
if ( !isset ( $browser ) ) { $browser = null; }
if ( ( $show_detailed_browser == 0 ) && ( $browser ) )
 {
  foreach ( $browser as $key => $value )
   {
    $temp_browser_trim = trim ( substr ( $key , 0 , strrpos ( $key , " " ) ) );
    if ( !isset ( $browser_simple [ $temp_browser_trim ] ) ) { $browser_simple [ $temp_browser_trim ] = 0; }
    $browser_simple [ $temp_browser_trim ] += $value;
   }
  $browser = $browser_simple;
 }
//------------------------------------------------------------------------------
// consolidates browser version to one minor version
if ( ( $show_detailed_browser == 1 ) && ( $browser ) )
 {
  foreach ( $browser as $key => $value )
   {
    if ( ( strpos ( $key , "." ) ) === false )
     {
      $temp_browser_trim = trim ( $key );
      if ( !isset ( $browser_simple [ $temp_browser_trim ] ) ) { $browser_simple [ $temp_browser_trim ] = 0; }
      $browser_simple [ $temp_browser_trim ] += $value;
     }
    else
     {
      $temp_browser_trim = trim ( substr ( $key , 0 , strpos ( $key , "." ) + 2 ) );
      if ( !isset ( $browser_simple [ $temp_browser_trim ] ) ) { $browser_simple [ $temp_browser_trim ] = 0; }
      $browser_simple [ $temp_browser_trim ] += $value;
     }
   }
  $browser = $browser_simple;
 }
unset ( $browser_simple );
//------------------------------------------------------------------------------
// check if details of every operating system should be displayed
if ( ( $show_detailed_os == 0 ) && ( $operating_system ) )
 {
  foreach ( $operating_system as $key => $value )
   {
    if ( strpos ( $key , " - " ) > 0 )
     {
      $temp_os_trim = trim ( substr ( $key , 0 , strrpos ( $key , " - " ) ) );
      if ( !isset ( $operating_system_simple [ $temp_os_trim ] ) ) { $operating_system_simple [ $temp_os_trim ] = 0; }
      $operating_system_simple [ $temp_os_trim ] += $value;
     }
    elseif ( strpos ( $key , " " ) > 0 )
     {
      $temp_os_trim = trim ( substr ( $key , 0 , strrpos ( $key , " " ) ) );
      if ( !isset ( $operating_system_simple [ $temp_os_trim ] ) ) { $operating_system_simple [ $temp_os_trim ] = 0; }
      $operating_system_simple [ $temp_os_trim ] += $value;
     }
    else
     {
      if ( !isset ( $operating_system_simple [ $key ] ) ) { $operating_system_simple [ $key ] = 0; }
      $operating_system_simple [ $key ] += $value;
     }
   }
  $operating_system = $operating_system_simple;
 }
unset ( $operating_system_simple );
//------------------------------------------------------------------------------
include ( "func/html_header.php" ); // include html header
//------------------------------------------------------------------------------
// include refresh funktion
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  include ( "func/func_refresh.php" ); // include function refresh
  echo '<script>refresh_display()</script>'."\n";
 }
//------------------------------------------------------------------------------
// change to admin session
if ( $_SESSION [ 'loggedin' ] != 'admin' )
 {
  echo '<div id="changeloggedin" class="session_change" style="display:none">
    <div class="header">
      <a class="close" href="#" onclick="changeloggedin();return false;" style="text-align:right; text-decoration:none; font-size:26px; margin-right:10px; color:#fff">&times;</a>
      '.$lang_login[6].'
    </div>
    <div class="info" style="font-size:12px">'.$lang_login[7].'</div>
    <form name="admin_status" action="#" method="post">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon"><span class="glyphicon glyphicon-lock fa-lg"></span></div>
          <input type="password" name="password" id="password" class="form-control" placeholder="'.$lang_login[3].'" autofocus>
          <input type="hidden" name="lang" value="'.$lang.'">
          <span class="input-group-btn">
            <button type="submit" class="btn"><span class="glyphicon glyphicon-ok"></span> OK</button>
          </span>
        </div>
      </div>
    </form>
  </div>
  ';
 }
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  //--------------------------------
  // display the stat responsive header
  echo '<div id="header-m" class="fixed-top">
    <div class="brand" style="margin-right:20px">
      <a href="https://www.php-web-statistik.de" target="_blank" style="float:left; margin-right:15px"><img src="images/system.png" style="height:50px; width:auto" alt="PHP Web Stat" title="PHP Web Stat"></a>
      <div class="brand-inline">
        <div class="brand-name">PHP Web Stat</div>
        <div class="brand-plus">Version '.$version_number.$revision_number.'</div>
      </div>
    </div>
    '; if ( $script_activity != 1 ) { echo '<div class="info-maintenance"><span class="glyphicon glyphicon-info-sign"></span> '.$lang_stat[4].'</div>'; }
    if ( $auto_update_check == 1 )
     {
      echo '
      <script src="https://www.php-web-statistik.de/checkversion.js"></script>
      <script>
      <!-- //
      if ( !CURRENT ) { document.write(\'\'); }
      else { if ( CURRENT > '.$version_number.$revision_number.' ) { document.write(\'<div class="info-update"><span class="glyphicon glyphicon-download"></span> '.$lang_stat[5].'</div>\'); } }
      // -->
      </script>'."\n";
     }
    echo '
    <span style="margin-right:auto"></span>
    ';
    //--------------------------------
    if ( $_SESSION [ 'loggedin' ] != 'admin' )
     {
      if ( $_SESSION [ 'loggedin' ] != 'client' )
       {
        echo '<a href="#" onclick="changeloggedin(); return false;" style="margin-top:4px"><span class="navico-status status-user glyphicon glyphicon-user" style="font-size:20px; cursor:pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Status (<b>User</b>)"></span></a>';
       }
      else
       {
        echo '<a href="#" onclick="changeloggedin(); return false;" style="margin-top:4px"><span class="navico-status status-client glyphicon glyphicon-user" style="font-size:20px; cursor:pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Status (<b>Client</b>)"></span></a>';
       }
     }
    else
     {
      echo '<span class="navico-status status-admin glyphicon glyphicon-user" style="font-size:20px; cursor:default" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Status (<b>Admin</b>)"></span>';
     }
    //--------------------------------
    echo '
    <span class="navico-info glyphicon glyphicon-info-sign" style="font-size:20px; cursor:default" data-toggle="tooltip" data-placement="bottom" data-html="true" title="'.$lang_stat[2].'<br><b>'.$stat_name.'</b><br><br>'.$lang_stat[3].': <b>'.$first_timestamp.'</b>"></span>
    <a href="#" class="navico-refresh" onclick="document.getElementById(\'fade-overlay-b\').style.display=\'block\'; start(creator_iframe,control_iframe)"><span class="glyphicon glyphicon-refresh" style="font-size:20px; top:3px" data-toggle="tooltip" data-placement="bottom" title="'.$lang_menue[4].'"></span></a>
    <a href="javascript:return false" class="navico-tabs tab-menu-toggle" style="outline:0"><span class="glyphicon glyphicon-th-list" style="font-size:26px; top:2px"></span></a>
    <a href="javascript:return false" class="navico-menue navbar-toggle" style="outline:0"><span class="glyphicon glyphicon-th" style="font-size:26px; top:2px"></span></a>
    <a class="navico-logout" onclick="location.href=\'index.php?parameter=logout\';"><i class="material-icons" style="font-size:34px; cursor:pointer">power_settings_new</i></a>
    <button type="submit" class="navbtn-logout btn" onclick="location.href=\'index.php?parameter=logout\';"><i class="material-icons md-16" style="top:3px">power_settings_new</i> '.$lang_menue[7].'</button>
  </div>  <!-- END header-m -->'."\n";
  //--------------------------------
 }
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  //--------------------------------
  // display the stat sidenav
  echo '<div id="sidenav">
    <div class="brand" style="position:relative; left:50%; transform: translateX(-50%); margin-bottom:30px;">
      <a href="https://www.php-web-statistik.de" target="_blank" style="float:left; margin-right:15px"><img src="images/system.png" style="height:50px; width:auto" alt="PHP Web Stat" title="PHP Web Stat"></a>
      <div class="brand-inline">
        <div class="brand-name">PHP Web Stat</div>
        <div class="brand-plus">fast, simple &amp; smart</div>
      </div>
    </div>
    <div class="clearfix"></div>
    ';
    if ( $auto_update_check == 1 )
     {
      echo '
      <script src="https://www.php-web-statistik.de/checkversion.js"></script>
      <script>
      <!-- //
      if ( !CURRENT ) { document.write(\'\'); }
      else { if ( CURRENT > '.$version_number.$revision_number.' ) { document.write(\'<div class="info-update" style="position:relative; left:50%; transform: translateX(-50%); display:inline-block; margin-bottom:20px;"><span class="glyphicon glyphicon-download"></span> '.$lang_stat[5].'</div>\'); } }
      // -->
      </script>'."\n";
     }
    echo '<div id="sidenav-navlink">
      <ul>
        <li class="active"><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab1\'); change(this); return false;">'.$lang_tab[1].'</a></li>
        <li><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab2\'); change(this); return false;">'.$lang_tab[2].'</a></li>
        <li><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab3\'); change(this); return false;">'.$lang_tab[3].'</a></li>
        <li><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab4\'); change(this); return false;">'.$lang_tab[4].'</a></li>
        <li><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab5\'); change(this); return false;">'.$lang_tab[5].'</a></li>
        <li><a href="javascript:return false" onclick="hideTAB(); showTAB(\'tab6\'); change(this); return false;">'.$lang_tab[6].'</a></li>
      </ul>
    </div>';
    //--------------------------------
    if ( $language == 'language/german.php' ) { $plugin_button = '<button type="button" class="plugin-info-button" onclick="window.open(\'https://www.php-web-statistik.de/index.php#plugins\', \'_blank\')">'.$lang_stat[7].'</button>'; } else { $plugin_button = '<button type="button" class="plugin-info-button" onclick="window.open(\'https://www.php-web-statistik.de/en/index.php#plugins\', \'_blank\')">'.$lang_stat[7].'</button>'; }
    //--------------------------------
    if ( ( !file_exists ('plugins/geoip/info.php' ) ) && ( !file_exists ('plugins/lasthits/info.php' ) ) ) { $plugin_info = '
    <div class="plugin-info">
      '.$lang_stat[6].'
      <div class="text-center">
        '.$plugin_button.'<br>
        <span>'.$lang_stat[8].'</span>
      </div>
    </div>'; }
    //--------------------------------
    $geoip_version = file ( "func/geoip/LocationIPversion.dat" );
    echo '<div id="sidenav-footer">
      '.$plugin_info.'
      <hr>
      Version '.$version_number.$revision_number.'<br>
      '.$lang_stat[1].': '.$geoip_version [0].'<br>
      Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="_blank" title="PHP Web Stat Webside">PHP Web Stat</a>
    </div>
  </div> <!-- END sidenav -->'."\n";
  //--------------------------------
 }
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  //--------------------------------
  // display the archive main / archive-nav
  if ( isset ( $_GET [ 'archive_save' ] ) )
   {
    $temp = substr ( $_GET [ 'archive_save' ] , strrpos ( $_GET [ 'archive_save' ] , "/" ) + 1 );
    $temp = substr ( $temp , 0 , strlen ($temp ) - 4 );
    $temp = explode ( "-" , $temp );
    $from_timestamp  = $temp [ 0 ];
    $until_timestamp = $temp [ 1 ];
    unset ( $temp );
   }
  else
   {
    $from_timestamp  = strip_tags ( $_GET [ 'from_timestamp'  ] );
    $until_timestamp = strip_tags ( $_GET [ 'until_timestamp' ] );
   }
  echo '<div id="archive-main">
  <div id="archive-nav" class="fixed-top">
    <div class="brand">
      <a href="https://www.php-web-statistik.de" target="_blank" style="float:left; margin-right:15px"><img src="images/system.png" style="height:50px; width:auto" alt="PHP Web Stat" title="PHP Web Stat"></a>
      <div class="brand-inline">
        <div class="brand-name">PHP Web Stat</div>
        <div class="brand-plus">'.$lang_archive[3].'</div>
      </div>
    </div>
    <span style="margin-right:auto"></span>';
    if ( isset ( $_GET [ 'archive' ] ) )
     {
      if ( $_SESSION [ 'loggedin' ] == 'admin' )
       {
        echo '
        <div style="margin-top:6px"><iframe name="creator" class="creator" src="func/func_archive_save.php?from_timestamp='.$from_timestamp.'&until_timestamp='.$until_timestamp.'" style="height:20px; padding:0; margin:0; border:0; background:transparent; overflow:hidden;"></iframe></div>
        <span class="glyphicon glyphicon-print" onclick="window.print();return false;" style="font-size:16px; margin-left:30px; top:-1px; cursor:pointer" title="'.$lang_menue[6].'"></span>
        <button type="button" class="btn" style="margin-left:30px" onclick="window.close();return false;" title="'.$lang_footer[3].'"><span class="glyphicon glyphicon-remove"></span></button>
        ';
       }
      else
       {
        echo '
        <div class="btn-group d-block d-xs-none">
          <button type="button" class="btn" onclick="window.print();return false;" title="'.$lang_menue[6].'"><span class="glyphicon glyphicon-print" style="font-size:14px"></span></button>
          <button type="button" class="btn" onclick="window.close();return false;" title="'.$lang_footer[3].'"><span class="glyphicon glyphicon-remove" style="font-size:14px"></span></button>
        </div>
        <button type="button" class="btn d-none d-xs-block" onclick="window.close();return false;" title="'.$lang_footer[3].'"><span class="glyphicon glyphicon-remove" style="font-size:14px"></span></button>
        ';
       }
     }
    else
     {
      $archive_files = read_dir ( "log/archive" );
      asort ( $archive_files );
      echo '
      <div class="btn-group d-block d-xs-none">
        <div class="btn-group">
          <button class="btn dropdown-toggle" type="button" id="archive_save" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            '.$lang_archive[3].'
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu scrollable-menu" aria-labelledby="archive_save">
            <li class="dropdown-header">'.$lang_archive[3].'</li>';
            foreach ( $archive_files as $value )
             {
              $temp = substr ( $value , strrpos ( $value , "/" ) + 1 );
              $temp = substr ( $temp , 0 , strlen ($temp ) - 4 );
              $temp = explode ( "-" , $temp );
              echo '<li><a href="index.php?archive_save='.$value.'&parameter=finished">'.date ( "Y-m-d" , trim ( $temp [ 0 ] ) ).' - '.date ( "Y-m-d" , trim ( $temp [ 1 ] )  ).'</a></li>';
             }
            echo '
          </ul>
        </div>
        <button type="button" class="btn" onclick="window.print();return false;" title="'.$lang_menue[6].'"><span class="glyphicon glyphicon-print" style="font-size:14px"></span></button>
        <button type="button" class="btn" onclick="window.close();return false;" title="'.$lang_footer[3].'"><span class="glyphicon glyphicon-remove" style="font-size:14px"></span></button>
      </div>
      <button type="button" class="btn d-none d-xs-block" onclick="window.close();return false;" title="'.$lang_footer[3].'"><span class="glyphicon glyphicon-remove" style="font-size:14px"></span></button>
      ';
     }
  echo '</div> <!-- END archive-nav -->
  <div class="archive-time-selection">'.$lang_archive[12].' '.date ( "d.m.Y - H:i:s " , $from_timestamp ).' '.$lang_archive[13].' '.date ( "d.m.Y - H:i:s " , $until_timestamp ).'</div>'."\n\n";
  //--------------------------------
 }
else
 {
  //--------------------------------
  // display the stat main / main-nav
  echo '<div id="main">
  <div id="main-nav">
    '; if ( $script_activity != 1 ) { echo '<div class="info-maintenance" style="margin-top:0; padding:6px 10px"><span class="glyphicon glyphicon-info-sign"></span> '.$lang_stat[4].'</div>'; } echo '
    <span style="margin-right:auto"></span>
    ';
    //--------------------------------
    if ( $_SESSION [ 'loggedin' ] != 'admin' )
     {
      if ( $_SESSION [ 'loggedin' ] != 'client' )
       {
        echo '<a href="#" onclick="changeloggedin(); return false;" style="margin-top:4px"><span class="navico-status status-user glyphicon glyphicon-user" style="font-size:20px; cursor:pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Status (<b>User</b>)"></span></a>';
       }
      else
       {
        echo '<a href="#" onclick="changeloggedin(); return false;" style="margin-top:4px"><span class="navico-status status-client glyphicon glyphicon-user" style="font-size:20px; cursor:pointer" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Status (<b>Client</b>)"></span></a>';
       }
     }
    else
     {
      echo '<span class="navico-status status-admin glyphicon glyphicon-user" style="font-size:20px; cursor:default" data-toggle="tooltip" data-placement="bottom" data-container="body" data-html="true" title="Status (<b>Admin</b>)"></span>';
     }
    //--------------------------------
    echo '
    <span class="navico-info glyphicon glyphicon-info-sign" style="font-size:20px; cursor:default" data-toggle="tooltip" data-placement="bottom" data-container="body" data-html="true" title="'.$lang_stat[2].'<br><b>'.$stat_name.'</b><br><br>'.$lang_stat[3].': <b>'.$first_timestamp.'</b>"></span>
    <a href="#" class="navico-refresh" onclick="document.getElementById(\'fade-overlay-b\').style.display=\'block\'; start(creator_iframe,control_iframe)"><span class="glyphicon glyphicon-refresh" style="font-size:20px; top:3px" data-toggle="tooltip" data-placement="bottom" title="'.$lang_menue[4].'"></span></a>
    <a href="javascript:return false" class="navico-menue navbar-toggle" style="outline:0"><span class="glyphicon glyphicon-th" style="font-size:26px; top:2px"></span></a>
    <button type="submit" class="navbtn-logout btn" onclick="location.href=\'index.php?parameter=logout\';"><i class="material-icons md-16" style="top:3px">power_settings_new</i> '.$lang_menue[7].'</button>
  </div> <!-- END main-nav -->'."\n";
  //--------------------------------
 }
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  //--------------------------------
  // load the hidden stat menue
  echo '
  <div id="navbar" class="popover">
    <div class="arrow"></div>
    <div id="popover-index">
      <ul class="clearfix">
        <li><a href="#" data-toggle="modal" data-target="#Modal-Archive" onclick="hide(\'navbar\')" title="'.$lang_menue[2].'"><img src="images/menue_icons/archive.png" alt="'.$lang_menue[2].'"></a></li>
        ';
        //--------------------------------
        if ( $_SESSION [ 'loggedin' ] == 'admin' )
         {
          if ( $language == "language/german.php"     ) { echo '<li><form id="admin_center" action="config/admin.php?lang=de"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/english.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=en"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/dutch.php"      ) { echo '<li><form id="admin_center" action="config/admin.php?lang=nl"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/italian.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=it"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/spanish.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=es"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/catalan.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=es-ct" method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/farsi.php"      ) { echo '<li><form id="admin_center" action="config/admin.php?lang=fa"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/danish.php"     ) { echo '<li><form id="admin_center" action="config/admin.php?lang=dk"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/french.php"     ) { echo '<li><form id="admin_center" action="config/admin.php?lang=fr"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/turkish.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=tr"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/hungarian.php"  ) { echo '<li><form id="admin_center" action="config/admin.php?lang=hu"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/portuguese.php" ) { echo '<li><form id="admin_center" action="config/admin.php?lang=pt"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/hebrew.php"     ) { echo '<li><form id="admin_center" action="config/admin.php?lang=he"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/russian.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=ru"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/serbian.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=rs"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/finnish.php"    ) { echo '<li><form id="admin_center" action="config/admin.php?lang=fi"    method="post"><input type="hidden" name="password" value="'.$adminpassword.'"></form><a href="#" onclick="document.getElementById(\'admin_center\').submit();" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
         }
        else
         {
          if ( $language == "language/german.php"     ) { echo '<li><a href="config/admin.php?lang=de"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/english.php"    ) { echo '<li><a href="config/admin.php?lang=en"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/dutch.php"      ) { echo '<li><a href="config/admin.php?lang=nl"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/italian.php"    ) { echo '<li><a href="config/admin.php?lang=it"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/spanish.php"    ) { echo '<li><a href="config/admin.php?lang=es"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/catalan.php"    ) { echo '<li><a href="config/admin.php?lang=es-ct" title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/farsi.php"      ) { echo '<li><a href="config/admin.php?lang=fa"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/danish.php"     ) { echo '<li><a href="config/admin.php?lang=dk"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/french.php"     ) { echo '<li><a href="config/admin.php?lang=fr"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/turkish.php"    ) { echo '<li><a href="config/admin.php?lang=tr"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/hungarian.php"  ) { echo '<li><a href="config/admin.php?lang=hu"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/portuguese.php" ) { echo '<li><a href="config/admin.php?lang=pt"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/hebrew.php"     ) { echo '<li><a href="config/admin.php?lang=he"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/russian.php"    ) { echo '<li><a href="config/admin.php?lang=ru"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/serbian.php"    ) { echo '<li><a href="config/admin.php?lang=rs"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
          if ( $language == "language/finnish.php"    ) { echo '<li><a href="config/admin.php?lang=fi"    title="'.$lang_menue[5].'"><img src="images/menue_icons/admin.png" alt="'.$lang_menue[5].'"></a></li>'; }
         }
        //--------------------------------
        if ( isset ( $_COOKIE [ 'dontcount' ] ) )
         {
          $cookie = '<a href="cookie.php" title="'.$lang_setcookie[1].'&#10;'.$lang_setcookie[2].'"><img src="images/menue_icons/dontcount.png" alt="'.$lang_setcookie[2].'"></a>';
         }
        else
         {
          $cookie = '<a href="cookie.php" title="'.$lang_setcookie[3].'&#10;'.$lang_setcookie[4].'"><img src="images/menue_icons/count.png" alt="'.$lang_setcookie[4].'"></a>';
         }
        //--------------------------------
        echo '
        <li><a href="#" onclick="window.print();return false;" title="'.$lang_menue[6].'"><img src="images/menue_icons/print.png" alt="'.$lang_menue[6].'"></a></li>
        <li>'.$cookie.'</li>
      </ul>
      <div class="title">'.$lang_menue[8].'</div>';
      if ( read_dir ( "plugins" ) ) { } else { echo '<div style="padding:15px 0 0; text-align:center">'.$lang_menue[9].'</div>'; }
      echo '
      <ul class="clearfix">
        '; echo plugin_include("link"); echo '
      </ul>
    </div>
  </div> <!-- END navbar / popover -->
  
  <div class="tabover">
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab1\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[1].'</a><br>
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab2\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[2].'</a><br>
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab3\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[3].'</a><br>
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab4\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[4].'</a><br>
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab5\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[5].'</a><br>
    <a href="javascript:return false;" onclick="hideTAB(); showTAB(\'tab6\'); hide(\'tabover\'); change(this); return false;">'.$lang_tab[6].'</a><br>
  </div> <!-- END tabover -->'."\n";
  //--------------------------------
 }
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  //--------------------------------
  // start archive main wrapper
  echo '<div id="archive-main-wrapper">'."\n";
  //--------------------------------
 }
else
 {
  //--------------------------------
  // start main wrapper
  echo '<div id="main-wrapper">'."\n";
  //--------------------------------
 }
//==============================================================================
// display the stat module grid
################################################################################
### START tab 1 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  echo '
  <div class="row m-auto">
  <div class="col-3"> <!-- start left col -->
  ';
 }
else
 {
  echo '
  <div class="changetab" id="tab1">
  <div class="row m-auto">
  <div class="col-3"> <!-- start left col -->
  ';
 }
//=============================
if ( !isset ( $visitor_year ) ) { $visitor_year = null; }
if ( $visitor_year )
 {
  $visitor_per_year = array_sum ( $visitor_year ) ;
 }
else
 {
  $visitor_per_year = 0;
 }
//----------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  if ( $visitor_day )
   {
    if ( $real_first_timestamp == 0 )
     { $average = 0; }
    else
     {
      $average = ( int ) round ( array_sum ( $visitor_day ) / ( ( int ) round ( ( time () - $real_first_timestamp ) / 86400 ) + 1 ) );
     }
   }
  else
   {
    $average = 0;
   }
  //-----------------------------
  // timestamp detection
  $time_stamp_temp = time ();
  $time_zone_offsets = [
    '+14h'    =>  50400,
    '+13.75h' =>  49500,
    '+13h'    =>  46800,
    '+12.75h' =>  45900,
    '+12h'    =>  43200,
    '+11.5h'  =>  41400,
    '+11h'    =>  39600,
    '+10.5h'  =>  37800,
    '+10h'    =>  36000,
    '+9.5h'   =>  34200,
    '+9h'     =>  32400,
    '+8h'     =>  28800,
    '+7h'     =>  25200,
    '+6.5h'   =>  23400,
    '+6h'     =>  21600,
    '+5.75h'  =>  20700,
    '+5.5h'   =>  19800,
    '+5h'     =>  18000,
    '+4.5h'   =>  16200,
    '+4h'     =>  14400,
    '+3.5h'   =>  12600,
    '+3h'     =>  10800,
    '+2h'     =>  7200,
    '+1h'     =>  3600,
    '-1h'     => -3600,
    '-2h'     => -7200,
    '-3h'     => -10800,
    '-3.5h'   => -12600,
    '-4h'     => -14400,
    '-4.5h'   => -16200,
    '-5h'     => -18000,
    '-6h'     => -21600,
    '-7h'     => -25200,
    '-8h'     => -28800,
    '-9h'     => -32400,
    '-9.5h'   => -34200,
    '-10h'    => -36000,
    '-11h'    => -39600,
    '-12h'    => -43200
  ];
  if ( isset ( $time_zone_offsets[$server_time] ) ) {
    $time_stamp_temp += $time_zone_offsets[$server_time];
  }
  //-----------------------------
  // if there is no visitor today
  if ( isset ( $visitor_day [ date ( "y/m/d" , $time_stamp_temp ) ] ) )
   {
   }
  else
   {
    $visitor_day [ date ( "y/m/d" , $time_stamp_temp ) ] = 0;
   }
  //-----------------------------
  // if there is no visitor yesterday
  if ( isset ( $visitor_day [ date ( "y/m/d" , strtotime ( "-1 day" , $time_stamp_temp ) ) ] ) )
   {
    $visitor_yesterday = $visitor_day [ date ( "y/m/d" , strtotime ( "-1 day" , $time_stamp_temp ) ) ];
   }
  else
   {
    $visitor_yesterday = 0;
   }
  //-----------------------------
  // if there is no visitor this month
  if ( isset ( $visitor_month [ date ( "Y/m" , $time_stamp_temp ) ] ) )
   {
   }
  else
   {
    $visitor_month [ date ( "Y/m" , $time_stamp_temp ) ] = 0;
   }
  //-----------------------------
  // if there is no visitor last month
  $visitor_lastmonth_count = date ( "j" , $time_stamp_temp ) + 1;
  $visitor_lastmonth_count = "-".$visitor_lastmonth_count." days";
  if ( isset ( $visitor_month [ date ( "Y/m" , strtotime ( $visitor_lastmonth_count ) ) ] ) )
   {
    $visitor_lastmonth = $visitor_month [ date ( "Y/m" , strtotime ( $visitor_lastmonth_count ) ) ];
   }
  else
   {
    $visitor_lastmonth = 0;
   }
  //-----------------------------
  display_overview ( $lang_overview[1] ,
    /* line 1 */   $lang_overview[2]  , $visitor_per_year ,
    /* line 2 */   $lang_overview[9]  , $stat_add_visitors ,
    /* line 3 */   $lang_overview[3]  , $visitor_day [ date ( "y/m/d" , $time_stamp_temp ) ] ,
    /* line 4 */   $lang_overview[4]  , $visitor_yesterday ,
    /* line 5 */   $lang_overview[5]  , $visitor_month [ date ( "Y/m" , $time_stamp_temp ) ] ,
    /* line 6 */   $lang_overview[6]  , $visitor_lastmonth ,
    /* line 7 */   $lang_overview[7]  , max ( $visitor_day ) ,
    /* line 8 */   $lang_overview[8]  , $average ,
    /* line 9 */   $lang_overview[10] , $pageimpressions ,
                   $display_width_overview );
  //-----------------------------
  unset ( $average );
  unset ( $visitor_yesterday );
  unset ( $visitor_lastmonth );
  unset ( $visitor_per_year  );
  unset ( $pageimpressions   );
  //-----------------------------
  // delete the year in visitor_day
  $delete_year = 0;
  //-----------------------------
 }
//----------------------------------------------------------------
if ( !isset ( $visitor_hour ) ) { $visitor_hour = array ("00:00" => "0","01:00" => "0","02:00" => "0","03:00" => "0","04:00" => "0","05:00" => "0","06:00" => "0","07:00" => "0","08:00" => "0","09:00" => "0","10:00" => "0","11:00" => "0","12:00" => "0","13:00" => "0","14:00" => "0","15:00" => "0","16:00" => "0","17:00" => "0","18:00" => "0","19:00" => "0","20:00" => "0","21:00" => "0","22:00" => "0","23:00" => "0"); }
if ( $display_show_hour != 0 )
 {
  $max_value = array_sum ( $visitor_hour );
  // if visitor_hour empty, set the hour values to zero
  ksort ( $visitor_hour );
  display ( "hour" , $lang_hour[1] , $lang_hour[2] , $lang_module[1] , $lang_module[2] , $visitor_hour , $display_width_hour , 24 , $lang_module[3] , $delete_year , $max_value , "x" , 0 , 0 , 0 , 0);
  unset ( $max_value );
 }
//=============================
echo '
</div> <!-- END left col -->
<div class="col-3"> <!-- start center col -->
';
//=============================
if ( !isset ( $visitor_day ) ) { $visitor_day = null; }
if ( ( $display_show_day != 0 ) && ( count ( $visitor_day ) >= 1 ) )
 {
  if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
   {
    $delete_year = 1; # the year has to be deleted
    //-----------------------------
    $max_value = array_sum ( $visitor_day );
    ksort ( $visitor_day );
    display ( "day" , $lang_day[1] , $lang_day[2] , $lang_module[1] , $lang_module[2] , $visitor_day , $display_width_day , count($visitor_day) , $lang_module[3] , $delete_year , $max_value , "x" , 0 , 0 , 0 , 0 );
    unset ( $max_value );
    $delete_year = 0;
   }
  else
   {
    $delete_year = 1; # the year has to be deleted
    //-----------------------------
    // get the actual day & the amount of days this month
    $temp_month           = date ( "m" );
    $temp_count_day_month = date ( "t" );
    $temp_year            = date ( "y" );
    //-----------------------------
    // set the days values to zero
    for ( $x = 1 ; $x <= $temp_count_day_month ; $x++ )
     {
      if ( ( $x <= 9 ) && ( !isset ( $visitor_day [ $temp_year."/".$temp_month."/0".$x ] ) ) )
       {
        $visitor_day [ $temp_year."/".$temp_month."/0".$x ] = 0;
       }
      if ( ( $x > 9 ) && ( !isset ( $visitor_day [ $temp_year."/".$temp_month."/".$x ] ) ) )
       {
        $visitor_day [ $temp_year."/".$temp_month."/".$x  ] = 0;
       }
     }
    unset ( $temp_month           );
    unset ( $temp_count_day_month );
    unset ( $temp_year            );
    //-----------------------------
    krsort ( $visitor_day );
    $temp_count_day_month = date ( "t" , time () );
    $max_value = array_sum ( $visitor_day );
    $visitor_day = array_slice ( $visitor_day , 0 , $temp_count_day_month );
    ksort ( $visitor_day );
    display ( "day" , $lang_day[1] , $lang_day[2] , $lang_module[1] , $lang_module[2] , $visitor_day , $display_width_day , $temp_count_day_month , $lang_module[3] , $delete_year , $max_value , "visitors_per_day" , 0 , 0 , 0 , 0 );
    unset ( $temp_count_day_month );
    unset ( $max_value            );
    $delete_year = 0;
   }
 }
//=============================
echo '
</div> <!-- END center col -->
<div class="col-3"> <!-- start right col -->
';
//=============================
if ( $display_show_weekday != 0 )
 {
  // sort the weekdays
  if ( !isset ( $visitor_weekday [ "1" ] ) ) { $visitor_weekday [ "1" ] = 0; }
  if ( !isset ( $visitor_weekday [ "2" ] ) ) { $visitor_weekday [ "2" ] = 0; }
  if ( !isset ( $visitor_weekday [ "3" ] ) ) { $visitor_weekday [ "3" ] = 0; }
  if ( !isset ( $visitor_weekday [ "4" ] ) ) { $visitor_weekday [ "4" ] = 0; }
  if ( !isset ( $visitor_weekday [ "5" ] ) ) { $visitor_weekday [ "5" ] = 0; }
  if ( !isset ( $visitor_weekday [ "6" ] ) ) { $visitor_weekday [ "6" ] = 0; }
  if ( !isset ( $visitor_weekday [ "0" ] ) ) { $visitor_weekday [ "0" ] = 0; }
  //-----------------------------
  $sort_weekday = array (
  $lang_weekday [ 3 ] => $visitor_weekday [ "1" ],
  $lang_weekday [ 4 ] => $visitor_weekday [ "2" ],
  $lang_weekday [ 5 ] => $visitor_weekday [ "3" ],
  $lang_weekday [ 6 ] => $visitor_weekday [ "4" ],
  $lang_weekday [ 7 ] => $visitor_weekday [ "5" ],
  $lang_weekday [ 8 ] => $visitor_weekday [ "6" ],
  $lang_weekday [ 9 ] => $visitor_weekday [ "0" ]
  );
  //-----------------------------
  $visitor_weekday = $sort_weekday;
  if ( !$visitor_weekday [ $lang_weekday [ 9 ] ] ) { $visitor_weekday [ $lang_weekday [ 9 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 3 ] ] ) { $visitor_weekday [ $lang_weekday [ 3 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 4 ] ] ) { $visitor_weekday [ $lang_weekday [ 4 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 5 ] ] ) { $visitor_weekday [ $lang_weekday [ 5 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 6 ] ] ) { $visitor_weekday [ $lang_weekday [ 6 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 7 ] ] ) { $visitor_weekday [ $lang_weekday [ 7 ] ] = 0; }
  if ( !$visitor_weekday [ $lang_weekday [ 8 ] ] ) { $visitor_weekday [ $lang_weekday [ 8 ] ] = 0; }
  unset ( $sort_weekday );
  $max_value = array_sum ( $visitor_weekday );
  display ( "weekdays" , $lang_weekday[1] , $lang_weekday[2] , $lang_module[1] , $lang_module[2] , $visitor_weekday , $display_width_weekday , 7 , $lang_module[3] , $delete_year , $max_value , "x" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
  //-----------------------------
 }
//-----------------------------
if ( !isset ( $visitor_month ) ) { $visitor_month = null; }
if ( ( $display_show_month != 0 ) && ( count ( $visitor_month ) >= 1 ) )
 {
  if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
   {
    $max_value = array_sum ( $visitor_month );
    ksort ( $visitor_month );
    display ( "month" , $lang_month[1] , $lang_month[2] , $lang_module[1] , $lang_module[2] , $visitor_month , $display_width_month , 12 , $lang_module[3] , $delete_year , $max_value , "visitors_per_month" , 0 , 0 , 0 , 0 );
    unset ( $max_value   );
   }
  else
   {
    $max_value = array_sum ( $visitor_month );
    // to change the year of month values in the display function
    $delete_year = 2;
    // get the actual year
    $temp_year = date ( "Y" );
    // set the month values to zero
    for ( $x = 1 ; $x <= 12 ; $x++ )
     {
      if ( ( $x <= 9 ) && ( !isset ( $visitor_month [ $temp_year."/0".$x ] ) ) )
       {
        $visitor_month [ $temp_year."/0".$x ] = 0;
       }
      if ( ( $x > 9 ) &&  ( !isset ( $visitor_month [ $temp_year."/".$x ] ) ) )
       {
        $visitor_month [ $temp_year."/".$x ] = 0;
       }
     }
    unset ( $temp_year );
    krsort ( $visitor_month );
    $visitor_month = array_slice ( $visitor_month , 0 , 12 );
    ksort ( $visitor_month );
    display ( "month" , $lang_month[1] , $lang_month[2] , $lang_module[1] , $lang_module[2] , $visitor_month , $display_width_month , 12 , $lang_module[3] , $delete_year , $max_value , "visitors_per_month" , 0 , 0 , 0 , 0 );
    unset ( $max_value   );
    unset ( $delete_year );
   }
 }
//-----------------------------
if ( !isset ( $visitor_year ) ) { $visitor_year = array (" ".date("Y")." " => "0"); }
if ( $display_show_year != 0 )
 {
  $max_value = array_sum ( $visitor_year );
  if ( $visitor_year == "" ){ $visitor_year = array (" ".date("Y")." " => "0"); }
  ksort ( $visitor_year );
  $count_year=count($visitor_year);
  if ( $count_year > $display_count_year ) { $count_year = $display_count_year; }
  display ( "year" , $lang_year[1] , $lang_year[2] , $lang_module[1] , $lang_module[2] , $visitor_year , $display_width_year , $count_year , $lang_module[3] , $delete_year , $max_value , "trends_year" , 0 , 0 , 0 , 1 );
  unset ( $max_value );
 }
//=============================
echo '</div> <!-- END right col -->'."\n";
################################################################################
### END tab 1 ###
### START tab 2 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  echo '</div> <!-- END row -->'."\n";
  if ( count ( $resolution ) >= 1 )
   {
    echo '<hr class="pagebreak page-break-archive">';
   }
  echo '
  <div class="row m-auto">
  <div class="col-2"> <!-- start left col -->
  ';
 }
else
 {
  echo '
  </div> <!-- END row -->
  </div> <!-- END tab1 -->
  <div class="changetab page-break" id="tab2">
  <div class="row m-auto">
  <div class="col-2"> <!-- start left col -->
  ';
 }
//=============================
if ( !isset ( $browser ) ) { $browser = array(); }
if ( ( $display_show_browser != 0 ) && ( count ( $browser ) >= 1 ) )
 {
  $max_value = array_sum ( $browser );
  arsort ( $browser );
  display ( "browser" , $lang_browser[1] , $lang_browser[2] , $lang_module[1] , $lang_module[2] , $browser , $display_width_browser , $display_count_browser , $lang_module[3] , $delete_year , $max_value , "pattern_browser.dta" , 0 , 1 , 0 , 0 );
  unset ( $max_value );
 }
//-----------------------------
if ( !isset ( $operating_system ) ) { $operating_system = array(); }
if ( ( $display_show_os != 0 ) && ( count ( $operating_system ) >= 1 ) )
 {
  $max_value = array_sum ( $operating_system );
  arsort ( $operating_system );
  display ( "os" , $lang_os[1] , $lang_os[2] , $lang_module[1] , $lang_module[2] , $operating_system , $display_width_os , $display_count_os , $lang_module[3], $delete_year , $max_value , "pattern_operating_system.dta" , 0 , 0 , 1 , 0 );
  unset ( $max_value );
 }
//=============================
echo '
</div> <!-- END left col -->
<div class="col-2"> <!-- start right col -->
';
//=============================
if ( !isset ( $resolution ) ) { $resolution = array(); }
if ( ( $display_show_resolution != 0 ) && ( count ( $resolution ) >= 1 ) )
 {
  $max_value = array_sum ( $resolution );
  arsort ( $resolution );
  display ( "resolution" , $lang_resolution[1] , $lang_resolution[2] , $lang_module[1] , $lang_module[2] , $resolution , $display_width_resolution , $display_count_resolution , $lang_module[3] , $delete_year , $max_value , "pattern_resolution.dta" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//-----------------------------
if ( !isset ( $color_depth ) ) { $color_depth = array(); }
if ( ( $display_show_colordepth != 0 ) && ( count ( $color_depth ) >= 1 ) )
 {
  $max_value = array_sum ( $color_depth );
  arsort ( $color_depth );
  display ( "color" , $lang_colordepth[1] , $lang_colordepth[2] , $lang_module[1] , $lang_module[2] , $color_depth , $display_width_colordepth , $display_count_colordepth , $lang_module[3], $delete_year , $max_value , "x" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
echo '</div> <!-- END right col -->'."\n";
################################################################################
### END tab 2 ###
### START tab 3 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  echo '</div> <!-- END row -->'."\n";
  if ( count ( $site_name ) >= 1 )
   {
    echo '<hr class="pagebreak page-break-archive">'."\n";
   }
 }
else
 {
  echo '
  </div> <!-- END row -->
  </div> <!-- END tab2 -->
  <div class="changetab page-break" id="tab3">
  ';
 }
//=============================
if ( !isset ( $site_name ) ) { $site_name = array(); }
if ( ( $display_show_site != 0 ) && ( count ( $site_name ) >= 1 ) )
 {
  $temp_site_name_array = array ();
  foreach ( $site_name as $key => $value )
   {
    if ( !isset ( $pattern_site_name [ $key ] ) ) { $pattern_site_name [ $key ] = null; }
    $temp_kill_temp = kill_special_chars ( pattern_matching_reverse ( "site_name_reverse" , $pattern_site_name [ $key ] ) );
    if ( !isset ( $temp_site_name_array [ $temp_kill_temp ] ) ) { $temp_site_name_array [ $temp_kill_temp ] = 0; }
    $temp_site_name_array [ $temp_kill_temp ] += $value;
   }
  $site_name = $temp_site_name_array;
  unset ( $temp_site_name_array );
  unset ( $temp_kill_temp );

  $max_value = array_sum ( $site_name );
  arsort ( $site_name );
  display ( "site" , $lang_site[1] , $lang_site[2] , $lang_module[1] , $lang_module[2] , $site_name , $display_width_site , $display_count_site , $lang_module[3] , $delete_year , $max_value , "site_name" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//-----------------------------
if ( !isset ( $entrysite ) ) { $entrysite = array(); }
if ( ( $display_show_entrysite != 0 ) && ( count ( $entrysite ) >= 1 ) )
 {
  $temp_entrysite_array = array ();
  foreach ( $entrysite as $key => $value )
   {
    if ( !isset ( $pattern_site_name [ $key ] ) ) { $pattern_site_name [ $key ] = null; }
    $temp_kill_temp = kill_special_chars ( pattern_matching_reverse ( "site_name_reverse" , $pattern_site_name [ $key ] ) );
    if ( !isset ( $temp_entrysite_array [ $temp_kill_temp ] ) ) { $temp_entrysite_array [ $temp_kill_temp ] = 0; }
    $temp_entrysite_array [ $temp_kill_temp ] += $value;
   }
  $entrysite = $temp_entrysite_array;
  unset ( $temp_entrysite_array );
  unset ( $temp_kill_temp );

  $max_value = array_sum ( $entrysite );
  arsort ( $entrysite );
  display ( "entrysite" , $lang_entrysite[1] , $lang_entrysite[2] , $lang_module[1] , $lang_module[2] , $entrysite , $display_width_entrysite , $display_count_entrysite , $lang_module[3] , $delete_year , $max_value , "entrysite" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
################################################################################
### END tab 3 ###
### START tab 4 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  if ( count ( $referer ) >= 1 )
   {
    echo '<hr class="pagebreak page-break-archive">'."\n";
   }
 }
else
 {
  echo '
  </div> <!-- END tab3 -->
  <div class="changetab page-break" id="tab4">
  ';
 }
//=============================
if ( !isset ( $referer ) ) { $referer = array(); }
if ( ( $display_show_referer != 0 ) && ( count ( $referer ) >= 1 ) )
 {
  $max_value = array_sum ( $referer );
  arsort  ( $referer );
  display ( "referer" , $lang_referer[1] , $lang_referer[2] , $lang_module[1] , $lang_module[2] , $referer , $display_width_referer , $display_count_referer , $lang_module[3] , $delete_year , $max_value , "referer" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
################################################################################
### END tab 4 ###
### START tab 5 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  if ( !isset ( $searchengines_archive ) ) { $searchengines_archive = array(); }
  if ( !isset ( $searchwords_archive   ) ) { $searchwords_archive = array();   }
  if ( ( count ( $searchengines_archive ) >= 1 ) || ( count ( $searchwords_archive ) >= 1 ) )
   {
    echo '<hr class="pagebreak page-break-archive">';
   }
  echo '
  <div class="row m-auto">
  <div class="col-2"> <!-- start left col -->
  ';
 }
else
 {
  echo '
  </div> <!-- END tab4 -->
  <div class="changetab page-break" id="tab5">
  <div class="row m-auto">
  <div class="col-2"> <!-- start left col -->
  ';
 }
//=============================
if ( !isset ( $searchengines_archive ) ) { $searchengines_archive = array(); }
if ( ( $display_show_searchengines != 0 ) && ( count ( $searchengines_archive ) >= 1 ) )
 {
  $max_value = array_sum ( $searchengines_archive );
  arsort ( $searchengines_archive );
  display ( "searchengines" , $lang_searchengine[1] , $lang_searchengine[2] , $lang_module[1] , $lang_module[2] , $searchengines_archive , $display_width_searchengines , $display_count_searchengines , $lang_module[3] , $delete_year , $max_value , "searchengines_archive" , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
echo '
</div> <!-- END left col -->
<div class="col-2"> <!-- start right col -->
';
//=============================
if ( !isset ( $searchwords_archive ) ) { $searchwords_archive = array(); }
if ( ( $display_show_searchwords != 0 ) && ( count ( $searchwords_archive ) >= 1 ) )
 {
  $max_value = array_sum ( $searchwords_archive );
  arsort ( $searchwords_archive );
  display ( "searchwords" , $lang_searchwords[1] , $lang_searchwords[2] , $lang_module[1] , $lang_module[2] , $searchwords_archive , $display_width_searchwords , $display_count_searchwords , $lang_module[3], $delete_year , $max_value , "searchwords_archive"  , 0 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
echo '</div> <!-- END right col -->'."\n";
################################################################################
### END tab 5 ###
### START tab 6 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  echo '
  </div> <!-- /END row -->';
  if ( count ( $country ) >= 1 )
   {
    echo '<hr class="pagebreak page-break-archive">'."\n";
   }
 }
else
 {
  echo '
  </div> <!-- END row -->
  </div> <!-- END tab5 -->
  <div class="changetab page-break" id="tab6">
  ';
 }
//=============================
if ( !isset ( $country ) ) { $country = array(); }
if ( ( $display_show_cc != 0 ) && ( count ( $country ) >= 1 ) )
 {
  $country_full = array ();
  foreach ( $country as $key => $value )
   {
    if ( ( $key == "unknown" ) || ( $key == "-" ) )
     {
      $country_full [ $lang_module[3] ] = $value;
     }
    else
     {
      $country_full [ $country_array [ $key ]." (".$key.")" ] = $value;
     }
   }
  $max_value = array_sum ( $country_full );
  arsort ( $country_full );
  display ( "country" , $lang_country[1] , $lang_country[2] , $lang_module[1] , $lang_module[2] , $country_full , $display_width_cc , $display_count_cc , $lang_module[3] , $delete_year , $max_value , "country" , 1 , 0 , 0 , 0 );
  unset ( $max_value );
 }
//=============================
################################################################################
### END tab 6 ###
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  echo '
  </div> <!-- END tab6 -->
  <script> showTAB(\'tab1\') </script>';
 }
//==============================================================================
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
  //--------------------------------
  // display the archive main-footer
  echo '
  <div id="main-footer">
    <div style="text-align:center; padding-top:10px">Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="_blank">PHP Web Stat</a> <span class="d-xs-none">&nbsp;<b>&middot;</b>&nbsp; '.$lang_footer[1].' '.timer_stop($timer_start).' '.$lang_footer[2].'.</span></div>
  </div>
  ';
  //--------------------------------
 }
else
 {
  //--------------------------------
  // display the stat main-footer
  if ( $db_active == 1 )
   {
    echo '
    <div id="main-footer">
      <div style="text-align:center; padding-top:3px">Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="_blank">PHP Web Stat</a> &nbsp;<b>&middot;</b>&nbsp; '.$lang_footer[1].' '.timer_stop($timer_start).' '.$lang_footer[2].'.<br><span style="font-size:9px">This product includes IP2Location LITE data available from <a href="https://lite.ip2location.com">https://lite.ip2location.com</a>.</span></div>
    </div>'."\n";
   }
  else
   {
    echo '
    <div id="main-footer" class="clearfix">
      <div style="width:110px; float:left; padding-top:11px; text-align:center"><iframe name="make_index" src="func/func_create_index.php" style="width:81px; height:16px; padding:1px; margin:0; border:1px solid #666"></iframe></div>
      <div class="d-block d-lg-none" style="text-align:center; padding-top:3px">'.$lang_footer[1].' '.timer_stop($timer_start).' '.$lang_footer[2].'.<br><span class="d-sm-none" style="font-size:9px">This product includes IP2Location LITE data available from <a href="https://lite.ip2location.com">https://lite.ip2location.com</a>.</span></div>
      <div class="d-none d-lg-block" style="text-align:center; padding-top:3px">Copyright &copy; '.$last_edit.' <a href="https://www.php-web-statistik.de" target="_blank">PHP Web Stat</a> &nbsp;<b>&middot;</b>&nbsp; '.$lang_footer[1].' '.timer_stop($timer_start).' '.$lang_footer[2].'.<br><span class="d-sm-none" style="font-size:9px">This product includes IP2Location LITE data available from <a href="https://lite.ip2location.com">https://lite.ip2location.com</a>.</span></div>
    </div>'."\n";
   }
  //--------------------------------
 }
//------------------------------------------------------------------------------
echo '
</div> <!-- END main-wrapper or archive-main-wrapper -->
</div> <!-- END main-->'."\n";
//------------------------------------------------------------------------------
if ( ( isset ( $_GET [ 'archive' ] ) ) || ( isset ( $_GET [ 'archive_save' ] ) ) )
 {
 }
else
 {
  //--------------------------------
  // display the responsive footer
  echo '
  <div id="footer-m" class="fixed-bottom clearfix">
    <div class="footer-cell">'; if ( $script_activity != 1 ) { echo '<i class="material-icons md-32" style="color:#c40000; top:0; opacity: 0.5; cursor:pointer" onclick="alert(\''.$lang_stat[4].'\');">error_outline</i>'; } else { echo '&nbsp;'; } echo '</div>
    <div class="footer-cell">
    ';
    //--------------------------------
    if ( $_SESSION [ 'loggedin' ] != 'admin' )
     {
      if ( $_SESSION [ 'loggedin' ] != 'client' )
       {
        echo '<a href="#" onclick="changeloggedin(); return false;"><span class="navico-status status-user glyphicon glyphicon-user" style="font-size:20px; margin-left:0; cursor:pointer" data-toggle="tooltip" data-placement="top" data-html="true" title="Status (<b>User</b>)"></span></a>';
       }
      else
       {
        echo '<a href="#" onclick="changeloggedin(); return false;"><span class="navico-status status-client glyphicon glyphicon-user" style="font-size:20px; margin-left:0; cursor:pointer" data-toggle="tooltip" data-placement="top" data-html="true" title="Status (<b>Client</b>)"></span></a>';
       }
     }
    else
     {
      echo '<span class="navico-status status-admin glyphicon glyphicon-user" style="font-size:20px; margin-left:0; opacity:0.5; cursor:default" data-toggle="tooltip" data-placement="top" data-html="true" title="Status (<b>Admin</b>)"></span>';
     }
    //--------------------------------
    echo '
    </div>
    <div class="footer-cell"><a href="javascript:return false;" onclick="document.getElementById(\'fade-overlay-b\').style.display=\'block\'; start(creator_iframe,control_iframe)"><span class="navico-refresh glyphicon glyphicon-refresh" style="font-size:20px; margin-left:0" title="'.$lang_menue[4].'"></span></a></div>
    <div class="footer-cell"><a class="navico-tabs tab-menu-toggle" style="margin-left:0" href="javascript:return false;"><i class="material-icons md-32" style="top:4px; cursor:pointer">menu</i></a></div>
  </div> <!-- END footer-m -->
  ';
  //--------------------------------
 }
//------------------------------------------------------------------------------
echo '
<div id="fade-overlay-w" class="overlay_white"></div>
<div id="fade-overlay-b" class="overlay_black"></div>

<!-- Modal Archive -->
<div class="modal fade" id="Modal-Archive" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" style="width:calc(100% - 20px); max-width:460px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <span class="modal-title"><b>'.$lang_archive[3].'</b></span>
      </div>
      <div class="modal-body">
        <iframe src="archive.php" id="archive_frame" style="width:100%; height:220px; margin:0; background:transparent; overflow:hidden; border:0"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm" onclick="reload(\'archive_frame\')"><span class="glyphicon glyphicon-refresh"></span> '.$lang_menue[4].'</button>
      </div>
    </div>
  </div>
</div> <!-- /#Modal-Archive -->'."\n";

echo plugin_include("modal"); echo '
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
  $(".navbar-toggle").click(function(e){
      e.stopPropagation();
      $(".popover").slideToggle(500);
  });
  $(document).click(function(e){
      var container = $(".popover");
      if (!container.is(e.target) && container.has(e.target).length === 0){
          container.slideUp(500);
      }
  });
  $(".popover a").click(function(){
      $(".popover").toggle(800);
  });
});

$(document).ready(function(){
  $(".tab-menu-toggle").click(function(e){
      e.stopPropagation();
      $(".tabover").slideToggle(500);
  });
  $(document).click(function(e){
      var container = $(".tabover");
      if (!container.is(e.target) && container.has(e.target).length === 0){
          container.slideUp(500);
      }
  });
  $(".tabover a").click(function(){
      $(".tabover").toggle(800);
  });
});

function reload() {
  document.getElementById("archive_frame").src += "";
}

function changeloggedin()
 {
  var changeloggedin = document.getElementById("changeloggedin")
  if (changeloggedin.style.display == "none")
   {
    document.getElementById("fade-overlay-b").style.display = "block";
    changeloggedin.style.display = "";
    document.admin_status.password.focus();
   }
  else
   {
    changeloggedin.style.display = "none";
    document.getElementById("fade-overlay-b").style.display = "none";
   }
 }

$(document).ready(function(){
  $(\'[data-toggle="tooltip"]\').tooltip();
});
'; echo plugin_include("js");
echo '</script>';
################################################################################
### print ###
/*
if ( isset ( $_GET [ 'print' ] ) && $_GET [ 'print' ] == 1 )
 {
  echo '<script> window.onload = window.print; window.print(); </script>';
 }
*/
//------------------------------------------------------------------------------
include ( "func/html_footer.php" ); // include html footer
//------------------------------------------------------------------------------
// kill all vars
unset ( $visitor               );
unset ( $visitor_hour          );
unset ( $visitor_day           );
unset ( $visitor_weekday       );
unset ( $visitor_month         );
unset ( $visitor_year          );
unset ( $browser               );
unset ( $operating_system      );
unset ( $resolution            );
unset ( $color_depth           );
unset ( $site_name             );
unset ( $referer               );
unset ( $country               );
unset ( $country_full          );
unset ( $searchengines_archive );
unset ( $searchwords_archive   );
unset ( $entrysite             );
//------------------------------------------------------------------------------
?>