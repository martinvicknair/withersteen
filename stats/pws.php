<?php header ( "Cache-Control: no-cache, must-revalidate" ); header ( "Expires: Sat, 26 Jul 2000 05:00:00 GMT" );
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
# Copyright � 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################
error_reporting(0);
//------------------------------------------------------------------------------
clearstatcache(); // empty the filecache to get the real live data
//------------------------------------------------------------------------------
include ( 'config/config.php' ); // include path to logfile
//------------------------------------------------------------------------------
if ( ( isset( $_COOKIE [ 'dontcount' ] ) ) && ( $_COOKIE [ 'dontcount' ] == 'ja' ) ) { exit; }
//------------------------------------------------------------------------------
if ( $script_activity != 1 ) { exit; } // if stat is set to maintenance mode
//------------------------------------------------------------------------------
if ( $db_active == 1 )
 {
  include ( 'config/config_db.php'     ); // include db prefix
  include ( 'func/func_db_connect.php' ); // include database connection
 }
################################################################################
// timestamp detection
//------------------------------------------------------------------------------
$time_stamp = time ();
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
  $time_stamp += $time_zone_offsets[$server_time];
}
################################################################################
// check for cache update (if enabled in admin center)
//------------------------------------------------------------------------------
if ( $cache_update == 0 )
 { }
else
 {
  // Get the latest timestamp of the cachecreator execution
  $timestamp_cache_update_file = fopen ( "log/timestamp_cache_update.dta" , "r" );
   $timestamp_cache_update_file_entry = fgets ( $timestamp_cache_update_file , 6000 );
  fclose ( $timestamp_cache_update_file );
  unset ( $timestamp_cache_update_file );

  // Run the Cache Update if the time difference matches
  $cache_update_frequency = $cache_update * 60;
  if ( ( time () - $timestamp_cache_update_file_entry ) > $cache_update_frequency )
   {
    //------------------------------------------------------------------
    // Set the timestamp for cache updates
    $cache_timestamp_file = fopen ( "log/timestamp_cache_update.dta" , "r+" );
    flock ( $cache_timestamp_file , LOCK_EX );
     ftruncate ( $cache_timestamp_file , 0 );
     fwrite ( $cache_timestamp_file , time() );
    flock ( $cache_timestamp_file , LOCK_UN );
    fclose ( $cache_timestamp_file );
    unset  ( $cache_timestamp_file );
    //------------------------------------------------------------------
    // Send javascript to browser for automatic cache update
    header("content-type: application/javascript");
    echo 'jsinfo = "'.$script_domain.'/'.$script_path.'cache_creator.php?pw=update";';
    echo "\ntry {\nvar script = document.createElementNS('http://www.w3.org/1999/xhtml','script');\n";
    echo "script.setAttribute('type', 'text/javascript');\n";
    echo "script.setAttribute('src', jsinfo);\n";
    echo "document.getElementsByTagName('body')[0].appendChild(script);\n";
    echo "}\ncatch(e) {\n";
    echo 'str = "<script type=\"text/javascript\" src=\""+jsinfo+"\"></script>"';
    echo "\n".'document.write(str+""); }';
    //------------------------------------------------------------------
   }
 }
################################################################################
// include plugin update functions
//------------------------------------------------------------------------------
include ( 'func/func_read_dir.php' );
//------------------------------------------
$plugin_dir_read = read_dir ( "plugins/" );
//------------------------------------------
foreach ( $plugin_dir_read as $value )
 {
  if ( file_exists ( "plugins/".$value."/cron_function.php" ) )
   {
    include ( 'plugins/'.$value.'/cron_function.php' );
   }
 }
################################################################################
// get ip address
//------------------------------------------------------------------------------
    if ( $get_ip_address == 1 )  { $ip_address = $_SERVER [ 'REMOTE_ADDR'            ]; }
elseif ( $get_ip_address == 2 )  { $ip_address = $_SERVER [ 'HTTP_X_REMOTECLIENT_IP' ]; }
elseif ( $get_ip_address == 3 )  { $ip_address = $_SERVER [ 'HTTP_X_FORWARDED_FOR'   ]; }
elseif ( $get_ip_address == 4 )  { $ip_address = $_SERVER [ 'HTTP_CLIENT_IP'         ]; }
elseif ( $get_ip_address == 5 )  { $ip_address = getenv   ( 'REMOTE_ADDR'            ); }
elseif ( $get_ip_address == 6 )  { $ip_address = getenv   ( 'HTTP_X_REMOTECLIENT_IP' ); }
elseif ( $get_ip_address == 7 )  { $ip_address = getenv   ( 'HTTP_X_FORWARDED_FOR'   ); }
elseif ( $get_ip_address == 8 )  { $ip_address = getenv   ( 'HTTP_CLIENT_IP'         ); }
  else { $ip_address = $_SERVER [ 'REMOTE_ADDR' ]; }
################################################################################
// check for ip-address exclude
//------------------------------------------------------------------------------
if ( strpos ( $ip_address , ":" ) > 0 ) // ipv6
 {
  // ipv6 detection will be included in future versions
 }
else // ipv4
 {
  for ( $x = 0 ; $x < count ( $exception_ip_addresses ) ; $x++ )
   {
    $ip_pattern = preg_replace( "/\./" , "\." , $exception_ip_addresses [ $x ] );
    $ip_pattern = preg_replace( "/\*/" , ".*" , $ip_pattern );
    if ( preg_match ( "/".$ip_pattern."/" , $ip_address ) ) { exit; }
   }
 }
unset ( $ip_pattern );
################################################################################
// if javascript tracking
//------------------------------------------------------------------------------
if ( ( isset( $_GET [ 'mode' ] ) ) && ( $_GET [ 'mode' ] == 'js' ) )
 {
  //------------------------------------------------------------------
  // SSL Fix by www.ct-designs.de
  if ( ( isset ( $_SERVER [ 'HTTPS' ] ) ) && ( $_SERVER [ 'HTTPS' ] == 'on' ) ) { $script_domain = str_replace ( "http:" , "https:" , $script_domain ); }
  //------------------------------------------------------------------
  foreach ( $exception_domain as $value ) // check if the call comes from within
   {
    if ( !isset ( $_SERVER [ 'HTTP_REFERER' ] ) ) { $_SERVER [ 'HTTP_REFERER' ] = null; }
    $arrUrl = parse_url ( strtolower ( $_SERVER [ 'HTTP_REFERER' ] ) );
    if ( !isset ( $arrUrl [ 'host' ] ) ) { $arrUrl [ 'host' ] = null; }
    if ( preg_match ("/\.".strtolower ( $value )."$/", ".".$arrUrl [ 'host' ] ) )
     {
      header("content-type: text/javascript"); // send javascript to browser

      $javascript_include_file = file_get_contents ( 'js/pws.js' ) ;
      $javascript_include_code = str_replace ( 'SCRIPT_DOMAIN'     , $script_domain , $javascript_include_file );
      $javascript_include_code = str_replace ( 'SCRIPT_PATH'       , $script_path   , $javascript_include_code );
      
      if ( $frames == 1 ) { $javascript_include_code = str_replace ( 'document.referrer' , 'top.document.referrer' , $javascript_include_code ); }
 
      echo $javascript_include_code;
      exit;    
     }
   }
  exit;
  //------------------------------------------------------------------
 }
else
 { if ( ( strpos ( strtolower ( $_SERVER [ 'PHP_SELF' ] ) , "pws.php" ) > 0 ) && ( !isset ($_GET [ 'js_url' ] ) ) ) { exit; } }
################################################################################
// if no cookie and no javascript, go on
//------------------------------------------------------------------------------
function track_kill_chars ( $value )
 {
  $search_pattern [ ] = '/\|/';
  $search_pattern [ ] = '/\%7C/';
  $search_pattern [ ] = '/\%7c/';
  $search_pattern [ ] = '/\$/';
  $search_pattern [ ] = '/\[/';
  $search_pattern [ ] = '/\]/';
  $search_pattern [ ] = '/\%5B/';
  $search_pattern [ ] = '/\%5D/';
  $search_pattern [ ] = '/\%24/';
  return preg_replace ( $search_pattern , '' , addslashes ( strip_tags ( $value ) ) );
 }
//------------------------------------------------------------------------------
if ( !isset ( $_GET [ 'js_agent'      ] ) ) { $_GET [ 'js_agent'      ] = null; }
if ( !isset ( $_GET [ 'js_resolution' ] ) ) { $_GET [ 'js_resolution' ] = null; }
if ( !isset ( $_GET [ 'js_color'      ] ) ) { $_GET [ 'js_color'      ] = null; }
if ( !isset ( $_GET [ 'ref'           ] ) ) { $_GET [ 'ref'           ] = null; }
if ( !isset ( $_GET [ 'js_referer'    ] ) ) { $_GET [ 'js_referer'    ] = null; }
if ( !isset ( $_GET [ 'js_url'        ] ) ) { $_GET [ 'js_url'        ] = null; }
if ( !isset ( $_GET [ 'js_bos'        ] ) ) { $_GET [ 'js_bos'        ] = null; }
//------------------------------------------------------------------------------
$js_resolution = track_kill_chars ( $_GET [ 'js_resolution' ] );
$js_color      = track_kill_chars ( $_GET [ 'js_color'      ] );
$js_referer    = track_kill_chars ( $_GET [ 'js_referer'    ] );
$js_url        = track_kill_chars ( $_GET [ 'js_url'        ] );
$js_bos        = track_kill_chars ( $_GET [ 'js_bos'        ] );
################################################################################
// check for referer exclude
//------------------------------------------------------------------------------
if ( trim ( $js_referer ) != "" )
 {
  $referer_original = $js_referer;
 }
if ( isset ( $referer_original ) && isset ( $block_referer ) )
 {
  if ( ( trim ( $referer_original ) != '' ) && ( $block_referer != '' ) )
   {
    $temp_block_referer = strtolower ( parse_url ( $referer_original , PHP_URL_HOST ) );

    foreach ( $block_referer as $value )
     {
      if ( strpos ( $temp_block_referer , strtolower ( $value ) ) !== FALSE )
       {
        exit;
       }
     }
     unset ( $value );
     unset ( $temp_block_referer );
   }
 }
unset ( $referer_original );
################################################################################
// check for bot exclude
//------------------------------------------------------------------------------
if ( isset ( $block_bots ) )
 {
  foreach ( $block_bots as $value )
   {
    if ( strpos ( strtolower ( $_SERVER [ 'HTTP_USER_AGENT' ] ) , strtolower ( $value ) ) !== FALSE )
     {
      exit;
     }
   }
  unset ( $value );
 }
################################################################################
// check other exclude
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
if ( ( strpos ( strtolower ( $js_referer ) , "images.google"  ) !== false ) && ( $google_images == 0 ) ) { exit; } // exit if google image search
if ( ( strpos ( strtolower ( $js_referer ) , "gclid"          ) !== false ) && ( $google_adwords == 0 ) ) { exit; } // exit if google adwords referer
if ( strpos ( strtolower ( $js_url     ) , "searchindex.html" ) !== false ) { exit; } // exit if google cache
if ( strpos ( strtolower ( $js_url     ) , "translate_cindex" ) !== false ) { exit; } // exit if google translate
if ( strpos ( strtolower ( $js_url     ) , "cseindex.htm"     ) !== false ) { exit; } // exit if google custom search
################################################################################
// country detection
//------------------------------------------------------------------------------
require_once ( 'func/func_geoip.php' );
if ( strpos ( $ip_address , ':' ) > 0 ) // ipv6
 {
  $db = new \IP2Location\Database('./func/geoip/LocationIPv6.bin', \IP2Location\Database::FILE_IO);
 }
else // ipv4
 {
  $db = new \IP2Location\Database('./func/geoip/LocationIP.bin', \IP2Location\Database::FILE_IO);
 }
$temp_records = $db->lookup($ip_address, \IP2Location\Database::ALL);
$country = $temp_records[ 'countryCode' ];
if ( $country == '-' ) { $country = ''; }
if ( $country != ''  ) { $country = strtolower ( $country ); }
unset ( $temp_records );
################################################################################
// sitename detection
//------------------------------------------------------------------------------
include ( 'func/func_pattern_matching.php' ); // include pattern maching detection
//------------------------------------------------------------------------------
// convert all slashes ('/') after the first ? to '%2F'
// Fix by hr3
function convert_slashes ( $value )
 {
  if ( strpos ( $value , "?" ) == FALSE ) { return $value; }
  else { return substr ( $value , 0 , strpos ( $value , "?" ) ).str_replace ( "/" , "%2F" , substr ( $value , strpos ( $value , "?" ) ) ); }
 }

$js_url     = convert_slashes ( $js_url     );
$js_referer = convert_slashes ( $js_referer );
//------------------------------------------------------------------------------
// exit if is a local hit e.g. C:/... or D:/...
if ( strpos ( strtolower ( $js_url ) , ":"  ) == 1 ) { exit; }
//------------------------------------------------------------------------------
if ( substr ( $js_url , 0 , 5 ) == 'https' ) { $temp_position = 8; } else { $temp_position = 7; }
$temp_site_name = substr ( strstr ( substr ( $js_url , $temp_position ) , "/" ) , 1 );
if ( substr ( $temp_site_name , 0 , 1 ) == "?" ) { $temp_site_name = $home_site_name.$temp_site_name; }
$temp_url = parse_url ( $js_url );

if ( isset ( $temp_url [ "query" ] ) ) { parse_str ( $temp_url [ "query" ] , $temp_parameter ); }

$temp_check_name_value = 0;
if ( dirname ( $temp_site_name ) != "." ) { $temp_dir_name = dirname ( $temp_site_name )."/"; }
else { $temp_dir_name = substr ( dirname ( $temp_site_name ) , 1 ); }
$temp_name = $temp_dir_name.substr ( basename ( $js_url ) , 0 , strpos ( basename ( $js_url ) , "?" ) );
$temp_check_name = null;

if ( isset ( $temp_url [ "query" ] ) )
 {
  foreach ( $temp_parameter as $key=>$value )
   {
    if ( in_array ( $key , $url_parameter ) )
     {
      $temp_check_name.= $key."=".$value."&";
      $temp_check_name_value = 1;
     }
   }
 }
if ( $temp_check_name_value == 1 )
 {
  $temp_check_name = $temp_name."?".substr ( $temp_check_name , 0 , strlen ( $temp_check_name ) - 1 );
  $site_name = pattern_matching ( "site_name" , $temp_check_name ); // check site name
 }
if ( $temp_check_name_value == 0 )
 {
  if ( strpos ( $temp_site_name , "?" ) > 0 ) { $temp_site_name = substr ( $temp_site_name , 0 , strpos ( $temp_site_name , "?" ) ); }
  if ( strpos ( $temp_site_name , "." ) === FALSE ) { $temp_site_name = $temp_site_name.$home_site_name; }
  $site_name = pattern_matching ( "site_name" , $temp_site_name ); // check site name
 }
unset ( $temp_url );
unset ( $temp_parameter );
unset ( $temp_name );
unset ( $temp_site_name );
unset ( $temp_check_name );
unset ( $temp_check_name_value );
unset ( $temp_position );
################################################################################
// referer detection
//------------------------------------------------------------------------------
$inside_referer = 0;
if ( trim ( $js_referer ) != "" )
 {
  if ( substr ( $js_referer , 0 , 5 ) == 'https' ) { $temp_position = 8; } else { $temp_position = 7; }
  foreach ( $exception_domain as $value_referer )
   {
    if ( strpos ( substr ( $js_referer , 0 , strpos ( $js_referer."/" , "/" , $temp_position ) ) , $value_referer ) !== FALSE )
     {
      $temp_site_name = substr ( strstr ( substr ( $js_referer , $temp_position ) , "/" ) , 1 );
      if ( substr ( $temp_site_name , 0 , 1 ) == "?" ) { $temp_site_name = $home_site_name.$temp_site_name; }
      $temp_url = parse_url ( $js_referer );

      if ( isset ( $temp_url [ "query" ] ) ) { parse_str ( $temp_url [ "query" ] , $temp_parameter ); }

      $temp_check_name_value = 0;
      if ( dirname ( $temp_site_name ) != "." ) { $temp_dir_name = dirname ( $temp_site_name )."/"; }
      else { $temp_dir_name = substr ( dirname ( $temp_site_name ) , 1 ); }
      $temp_name = $temp_dir_name.substr ( basename ( $js_referer ) , 0 , strpos ( basename ( $js_referer ) , "?" ) );
      $temp_check_name = null;

      if ( isset ( $temp_url [ "query" ] ) )
       {
        foreach ( $temp_parameter as $key=>$value )
         {
          if ( in_array ( $key , $url_parameter ) )
           {
            $temp_check_name.= $key."=".$value."&";
            $temp_check_name_value = 1;
           }
         }
       }
      if ( $temp_check_name_value == 1 )
       {
        $temp_check_name = $temp_name."?".substr ( $temp_check_name , 0 , strlen ( $temp_check_name ) - 1 );
        $js_referer = "http://www.".$value_referer."/".$temp_check_name;
       }
      if ( $temp_check_name_value == 0 )
       {
        if ( strpos ( $temp_site_name , "?" ) > 0 )       { $temp_site_name = substr ( $temp_site_name , 0 , strpos ( $temp_site_name , "?" ) ); }
        if ( strpos ( $temp_site_name , "." ) === FALSE ) { $temp_site_name = $temp_site_name.$home_site_name; }
        $js_referer = "http://www.".$value_referer."/".$temp_site_name;
       }
      $inside_referer = 1;
      unset ( $temp_url );
      unset ( $temp_parameter );
      unset ( $temp_name );
      unset ( $temp_site_name );
      unset ( $temp_check_name );
      unset ( $temp_check_name_value );
      unset ( $temp_position );
     }
   }
 }
################################################################################
// external referer detection
//------------------------------------------------------------------------------
if ( $inside_referer == 0 )
 {
  $special_referer_url = $js_referer;
  if ( substr ( $special_referer_url , 0 , 5 ) == 'https' ) { $temp_position = 8; } else { $temp_position = 7; }
  $special_referer_url_parameter = array ( "q" , "search" , "query" , "ask" , "terms" , "key" , "qkw" , "su" , "dt" , "Keywords" , "origq" , "catId" );
  $special_referer_temp_site_name = substr ( strstr ( substr ( $special_referer_url , $temp_position ) , "/" ) , 1 );
  $special_referer_temp_url = parse_url ( $special_referer_url );

  if ( isset ( $special_referer_temp_url [ "query" ] ) ) { parse_str ( $special_referer_temp_url [ "query" ] , $special_referer_temp_parameter ); }

  $special_referer_temp_check_name_value = 0;
  $special_referer_temp_name = substr ( basename ( $special_referer_url ) , 0 , strpos ( basename ( $special_referer_url ) , "?" ) );
  $special_referer_temp_check_name = null;

  if ( isset ( $special_referer_temp_url [ "query" ] ) )
   {
    foreach ( $special_referer_temp_parameter as $key=>$value )
     {
      if ( in_array ( $key , $special_referer_url_parameter ) )
       {
        $special_referer_temp_check_name.= $key."=".$value."&";
        $special_referer_temp_check_name_value = 1;
       }
     }
   }
  if ( $special_referer_temp_check_name_value == 1 )
   {
    $js_referer = dirname ( $special_referer_url )."/".$special_referer_temp_name."?".substr ( $special_referer_temp_check_name , 0 , strlen ( $special_referer_temp_check_name ) - 1 );
   }
  unset ( $special_referer_temp_check_name );
  unset ( $special_referer_temp_check_name_value );
  unset ( $special_referer_temp_name );
  unset ( $special_referer_temp_url );
  unset ( $special_referer_temp_site_name );
  unset ( $special_referer_url );
  unset ( $special_referer_url_parameter );
  unset ( $temp_position );
 }
//------------------------------------------------------------------------------
// if referer is not empty, saving
if ( $db_active == 1 )
 {
  if ( trim ( $js_referer ) != "" )
   {
    if ( strpos ( $js_referer , "translate.google" ) > 0 ) { $referer = pattern_matching ( "referrer" , "http://translate.google.com" ); } // check pattern referer
    else
     {
      $db_connection = mysqli_connect ( $db_host , $db_user , $db_password , $db_name );
      $referer = pattern_matching ( "referrer" , mysqli_real_escape_string ( $db_connection , $js_referer ) ); // check pattern referer
 	 }
   }
  else { $referer = pattern_matching ( "referrer" , "---" ); } // if referer emty, referer = ---
 }
else
 {
  if ( trim ( $js_referer ) != "" )
   {
    if ( strpos ( $js_referer , "translate.google" ) > 0 ) { $referer = pattern_matching ( "referer" , "http://translate.google.com" ); } // check pattern referer
    else { $referer = pattern_matching ( "referer" , $js_referer ); } // check pattern referer
   }
  else { $referer = ""; }
 }
################################################################################
// os and browser detection
//------------------------------------------------------------------------------
// Windows 11 Detection
$js_agent_object = json_decode ( base64_decode ( $js_bos ) );
if ( ( $js_agent_object -> { 'platform' } == "Windows" ) && ( substr ( $js_agent_object -> { 'platformVersion' } , 0 , 2 ) > 10 ) )
 {
  $js_agent = "win11";   
 }
//------------------------------------------------------------------------------
include ( 'func/func_mobile_detect.php' ); // include mobile device
include ( 'func/func_useragent.php'     ); // include useragent (os, browser)
//------------------------------------------------------------------------------
use Jenssegers\Agent\Agent;
$agent = new Agent();
$browser                  = $agent->browser();
$browser_version          = $agent->version($browser);
$operating_system         = $agent->platform();
if ( ( isset( $js_agent ) ) && ( $js_agent == "win11" ) )
 { $operating_system_version = "11.0"; }
else
 { $operating_system_version = $agent->version($operating_system); }
$browser_version          = str_replace( "_", ".", $browser_version );
$browser                  = strip_tags ( $browser.' '.$browser_version );
$operating_system_version = str_replace( "_", ".", $operating_system_version );
$operating_system         = strip_tags ( $operating_system.' '.$operating_system_version );
//------------------------------------------------------------------------------
$browser          = pattern_matching ( "browser"          , $browser          ); // check pattern browser
$operating_system = pattern_matching ( "operating_system" , $operating_system ); // check pattern operating system
$resolution       = pattern_matching ( "resolution"       , $js_resolution    ); // check pattern resolution
//------------------------------------------------------------------------------
// check if the call comes from within
$write_logfile_entry = 0;
foreach ( $exception_domain as $value )
 {
  if ( strpos ( $js_url , $value ) > 0     ) { $write_logfile_entry = 1; }
  if ( substr ( $value , 0 , 4 ) == "xn--" ) { $write_logfile_entry = 1; }
 }
################################################################################
// write the log entries
//------------------------------------------------------------------------------
if ( strpos ( $ip_address , ":" ) > 0 ) // ipv6
 {
  if ( $hash_ip_address == 1 ) { $ip_address = substr ( $ip_address , 0 , strpos ( $ip_address , ":" , 5 ) + 1 ).substr ( md5 ( $ip_address ) , 0 , 10 ); }
  if ( $hash_ip_address == 2 ) { $ip_address = substr ( md5 ( $ip_address ) , 0 , 15 ); }
 }
else // ipv4
 {
  if ( $hash_ip_address == 1 ) { $ip_address = substr ( $ip_address , 0 , strrpos ( $ip_address , "." ) + 1 ).substr ( md5 ( $ip_address ) , 0 , 5 ); }
  if ( $hash_ip_address == 2 ) { $ip_address = substr ( md5 ( $ip_address ) , 0 , 15 ); }
 }
//------------------------------------------------------------------------------
if ( $write_logfile_entry == 1 )
 {
  if ( $db_active == 1 )
   {
    //------------------------------------------------------------------
    if ( $browser          == "" ) { $browser          = "1";       } // 1 = unknown
    if ( $operating_system == "" ) { $operating_system = "1";       } // 1 = unknown
    if ( $site_name        == "" ) { $site_name        = "1";       } // 1 = ---
    if ( $resolution       == "" ) { $resolution       = "1";       } // 1 = unknown
    if ( $js_color         == "" ) { $js_color         = "0";       } // 0 = unknown
    if ( $country          == "" ) { $country          = "unknown"; }
    // write the same entry into the database
    $query = "INSERT INTO ".$db_prefix."_main VALUES (NULL,1,".date("Y",$time_stamp).",".date("m",$time_stamp).",".date("W",$time_stamp).",".date("d",$time_stamp).",".date("H",$time_stamp).",".date("i",$time_stamp).",".$time_stamp.",'".$ip_address."',".$browser.",".$operating_system.",".$site_name.",".$referer.",".$resolution.",".$js_color.",'".$country."')";
    $result = db_query ( $query , 0 , 0 );
    //------------------------------------------------------------------
   }
  else
   {
    //------------------------------------------------------------------
    // write the same entry into the logdb file
    $log_file = fopen ( "log/logdb.dta" , "a+" );
     fwrite ( $log_file , $time_stamp."|".$ip_address."|".$browser."|".$operating_system."|".$site_name."|".$referer."|".$resolution."|".$js_color."|".$country."\n" ); # write log entry
    fclose ( $log_file ); unset ( $log_file );

    // write the same entry into the logdb_backup file
    $log_file = fopen ( "log/logdb_backup.dta" , "a+" );
     fwrite ( $log_file , $time_stamp."|".$ip_address."|".$browser."|".$operating_system."|".$site_name."|".$referer."|".$resolution."|".$js_color."|".$country."\n" ); # write log entry
    fclose ( $log_file ); unset ( $log_file );

    // write the last timestamp to last_timestamp.dta
    $log_file = fopen ( "log/last_timestamp.dta" , "r+" );
    flock ( $log_file , LOCK_EX );
     ftruncate ( $log_file , 0 );
     fwrite ( $log_file , $time_stamp );
    flock ( $log_file , LOCK_UN );
    fclose ( $log_file ); unset ( $log_file );
    //------------------------------------------------------------------
   }
 }
//------------------------------------------------------------------------------
?>