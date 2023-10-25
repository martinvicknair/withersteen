<?php
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     20.0                                                     #
# File-Release-Date:  23/04/04                                                 #
# Official web site and latest version:    http://www.php-web-statistik.de     #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright � 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
include ( 'config/config.php' ); // include path to logfile
//------------------------------------------------------------------------------
// check if the cronjob parameter (password) is given, else check for the session
$loggedin = 0;
if ( !isset ( $_GET [ 'pw' ] ) )
 {
  @session_start(); if ( $_SESSION [ 'hidden_stat' ] != md5_file ( 'config/config.php' ) ) { include ( 'func/func_error.php' ); exit; }
 }
else
 {
  if ( ( md5 ( $adminpassword ) != md5 ( $_GET [ 'pw' ] ) ) && ( $_GET [ 'pw' ] != 'update' ) ) { include ( 'func/func_error.php' ); exit; }
  else { $loggedin = 1; }
 }
//------------------------------------------------------------------------------
@ini_set ( "max_execution_time","false"      ); // set the script time
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
include ( 'func/func_pattern_reverse.php'    ); // include pattern reverse function
include ( 'func/func_pattern_matching.php'   ); // include pattern matching function
include ( 'func/func_kill_special_chars.php' ); // include umlaut function
//------------------------------------------------------------------------------
if ( $db_active == 1 )
 {  }
else
 {
  if ( $loggedin == 1 ) { $logfile_choosed = "log/logdb.dta"; }
  else
   {
        if ( $_GET [ 'loadfile' ] == 1 ) { $logfile_choosed = "log/logdb.dta";        }
    elseif ( $_GET [ 'loadfile' ] == 2 ) { $logfile_choosed = "log/logdb_backup.dta"; }
      else { $logfile_choosed = "log/logdb.dta"; }
   }
 }
//------------------------------------------------------------------------------
$write_cache          = 0;
$last_logfile_entry   = 0;
$last_memory_address  = 0;
$cache_memory_address = 0;
//------------------------------------------------------------------------------
// set the hour values to zero
for ( $x = 0 ; $x < 24 ; $x++ )
 {
  if ( $x <= 9 ) { $visitor_hour [ "0".$x.":00" ] = 0; }
  else { $visitor_hour [ $x.":00" ] = 0; }
 }
################################################################################
### archive function ###
if ( isset ( $_GET [ 'archive' ] ) )
 {
  include ( 'log/cache_time_stamp_archive.php' );   // include the last cache timestamp
  if ( !isset ( $cache_time_stamp ) ) { $cache_time_stamp = 0; }

  include ( 'log/cache_visitors_archive.php'   );   // include the saved arrays
  $temp_interval   = explode ( "-" , strip_tags ( $_GET [ 'archive' ] ) ); // get the posted interval
  $from_timestamp  = trim ( $temp_interval [ 0 ] ); // from timestamp
  $until_timestamp = trim ( $temp_interval [ 1 ] ); // until timestamp
  if ( $db_active == 1 )
   {
    if ( !isset ( $cache_time_stamp ) ) { $cache_time_stamp = 0; }
   }
  else
   {
    include ( 'log/index_days.php' );
    if ( array_key_exists ( $from_timestamp , $index_days ) ) { $cache_memory_address = $index_days [ $from_timestamp ]; } else { $cache_memory_address = 0; }
   }
 }
else
 {
  if ( $db_active != 1 ) { include ( 'log/cache_memory_address.php' ); } // include physical address of last read entry
  include ( 'log/cache_time_stamp.php' ); // include the last timestamp
  include ( 'log/cache_visitors.php'   ); // include the saved arrays
  $from_timestamp  = 1;                   // from timestamp
  if ( $db_active == 1 )
   {
    //------------------------------
    $query           = "SELECT MAX(timestamp) FROM ".$db_prefix."_main";
    $result          = db_query ( $query , 1 , 0 );
    $until_timestamp = $result[0][0];
    if ( !isset ( $cache_time_stamp ) ) { $cache_time_stamp = 0; }
    //------------------------------
   }
  else
   {
    //------------------------------
    $until_timestamp = 999999999999999999; // until timestamp
    //------------------------------
   }
 }
################################################################################
### fill values ###
if ( !isset ( $visitor ) ) { $visitor = array ( ); }
################################################################################
### search engine terms ###
function get_search_terms ( $value )
 {
  //----------------------------------------------------------------------------
  if ( stripos ( $value , "https://google."             ) !== FALSE ) { return "Google";                 }
  if ( stripos ( $value , "https://www.google."         ) !== FALSE ) { return "Google";                 }
  if ( stripos ( $value , "tpc.googlesyndication.com"   ) !== FALSE ) { return "Google Display Network"; }
  if ( stripos ( $value , "googleads.g.doubleclick.net" ) !== FALSE ) { return "Google Display Network"; }
  if ( stripos ( $value , "https://bing."               ) !== FALSE ) { return "Bing";                   }
  if ( stripos ( $value , "https://www.bing."           ) !== FALSE ) { return "Bing";                   }
  if ( stripos ( $value , "https://duckduckgo."         ) !== FALSE ) { return "DuckDuckGo";             }
  if ( stripos ( $value , "https://www.duckduckgo."     ) !== FALSE ) { return "DuckDuckGo";             }
  if ( stripos ( $value , "yahoo.com"                   ) !== FALSE ) { return "Yahoo";                  }
  if ( stripos ( $value , "search.yahoo.com"            ) !== FALSE ) { return "Yahoo";                  }
  if ( stripos ( $value , "https://yandex."             ) !== FALSE ) { return "Yandex";                 }
  if ( stripos ( $value , "https://www.yandex."         ) !== FALSE ) { return "Yandex";                 }
  if ( stripos ( $value , "https://ask."                ) !== FALSE ) { return "Ask.com";                }
  if ( stripos ( $value , "https://www.ask."            ) !== FALSE ) { return "Ask.com";                }
  if ( stripos ( $value , "https://search."             ) !== FALSE ) { return "Search";                 }
  if ( stripos ( $value , "https://www.search."         ) !== FALSE ) { return "Search";                 }
  if ( stripos ( $value , "web.de"                      ) !== FALSE ) { return "WEB.de";                 }
  if ( stripos ( $value , "https://aol."                ) !== FALSE ) { return "AOL";                    }
  if ( stripos ( $value , "https://www.aol."            ) !== FALSE ) { return "AOL";                    }
  if ( stripos ( $value , "https://lycos."              ) !== FALSE ) { return "Lycos";                  }
  if ( stripos ( $value , "https://www.lycos."          ) !== FALSE ) { return "Lycos";                  }
  //----------------------------------------------------------------------------
 }
################################################################################
### core function ###
if ( $db_active == 1 )
 {
  //----------------------------------------------------------------------------
  // get the data from the stat_main table
  if ( isset ( $_GET [ 'archive' ] ) )
   {
    $query = "SELECT ".$db_prefix."_main.timestamp,".$db_prefix."_main.ip_address,".$db_prefix."_main.browser,".$db_prefix."_main.operating_system,".$db_prefix."_main.site_name,".$db_prefix."_main.referrer,".$db_prefix."_main.resolution,".$db_prefix."_main.color_depth,".$db_prefix."_main.country_code FROM ".$db_prefix."_main WHERE ".$db_prefix."_main.timestamp > ".$cache_time_stamp." AND ".$db_prefix."_main.timestamp BETWEEN ".$from_timestamp." AND ".$until_timestamp." ORDER BY ".$db_prefix."_main.timestamp LIMIT 0,".$creator_number;
   }
  else
   {
    $query = "SELECT ".$db_prefix."_main.timestamp,".$db_prefix."_main.ip_address,".$db_prefix."_main.browser,".$db_prefix."_main.operating_system,".$db_prefix."_main.site_name,".$db_prefix."_main.referrer,".$db_prefix."_main.resolution,".$db_prefix."_main.color_depth,".$db_prefix."_main.country_code FROM ".$db_prefix."_main WHERE ".$db_prefix."_main.timestamp > ".$cache_time_stamp." ORDER BY ".$db_prefix."_main.timestamp LIMIT 0,".$creator_number;
   }

  $result  = db_query ( $query , 1 , 0 );
  $counter = count ( $result );
  //------------------------------------------------------------------------------
  // 0            1                2               3                  4            5                                   6            7             8
  // timestamp    ip_address       browser         operating_system   site_name    referrer                            resolution   color_depth   country_code
  // 1200183752   80.137.211.116   Firefox 2.0.0   Windows Vista      index.html   http://www.bluecay.com/index.html   1280x800     32            de
  //------------------------------------------------------------------------------
  if ( $counter == 0 )
   {
    $loader_finished = 1;
   }
  else
   {
    //------------------------------------------------------------------
    for ( $x = 0 ; $x <= count ( $result ) - 1 ; $x++ )
     {
      //------------------------------------------------------------------
      $write_cache = 1;                           // if new entries are found
      $last_logfile_entry = $result [ $x ] [ 0 ]; // last logfile entry for the cache

      // count site_name without timestamp
      if ( isset ( $site_name [ $result [ $x ] [ 4 ] ] ) ) { $site_name [ $result [ $x ] [ 4 ] ]++; } else { $site_name [ $result [ $x ] [ 4 ] ] = 1; }

      if ( !isset ( $pattern_referer [ $result[$x][5] ] ) ) { $pattern_referer [ $result[$x][5] ] = null; }
      $temp_referer = $pattern_referer [ $result[$x][5] ];
      //-----------------------
      if ( $temp_referer == "---" ) { $temp_referer = ""; }
      $exception_domain_found = 0;
      //-----------------------
      // Check for https referer
      if ( $temp_referer == "" )
       { $position_http = 0; }
      else
       { if ( $temp_referer [4] == "s" ) { $position_http = 8; } else { $position_http = 7; } }
      //-----------------------
      if ( ( !empty ( $temp_referer ) ) && ( $temp_referer != "---" ) )
       {
        foreach ( $exception_domain as $value )
         {
          if ( strpos ( substr ( $temp_referer , 0 , strpos ( $temp_referer."/" , "/" , $position_http ) ) , $value ) !== FALSE )
           {
            $exception_domain_found = 1;
           }
         }
       }
      //-----------------------
      if ( ( $pattern_referer [ $result[$x][5] ] != "---" ) && ( $exception_domain_found == 0 ) ) // if there is no referer and the referer is not found in the exception domain array
       {
        //---------------------------
        $exception_domain_found = 0; // set back that it is an internal referer
        //---------------------------
        // get search engine
        $searchengine = get_search_terms ( $temp_referer );
        //---------------------------------------------------------------------
        if ( trim ( $searchengine ) != "" )
         {
          if ( isset ( $searchengines_archive [ $searchengine ] ) ) { $searchengines_archive [ $searchengine ]++; }
          else { $searchengines_archive [ $searchengine ] = 1; }
         }
        if ( trim ( $search_words_temp ) != "" )
         {
          if ( isset ( $searchwords_archive [ trim ( $search_words_temp ) ] ) ) { $searchwords_archive [ trim ( $search_words_temp ) ]++; }
          else { $searchwords_archive [ trim ( $search_words_temp ) ] = 1; }
         }
        //---------------------------------------------------------------------
        if ( $show_detailed_referer == 0 )
         {
          $referer [ substr ( $pattern_referer [ $result[$x][5] ] , 0 , strpos ( substr ( $pattern_referer [ $result[$x][5] ] , 7 ) , "/" ) + 7 ) ]++;
         }
        else
         {
          $special_referer_url = $pattern_referer [ $result[$x][5] ];
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
            $special_referer_temp_check_name = dirname ( $special_referer_url )."/".$special_referer_temp_name."?".substr ( $special_referer_temp_check_name , 0 , strlen ( $special_referer_temp_check_name ) - 1 );
           }
          if ( $special_referer_temp_check_name_value == 0 )
           {
            $special_referer_temp_check_name = $pattern_referer [ $result[$x][5] ];
           }

          if ( isset ( $referer [ $special_referer_temp_check_name ] ) )
           {
            $referer [ $special_referer_temp_check_name ]++; // count referer without timestamp
           }
          else
           {
            $referer [ $special_referer_temp_check_name ] = 1;
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
        // save the entry site
        if ( isset ( $entrysite [ $result[$x][4] ] ) ) { $entrysite [ $result[$x][4] ]++; } else { $entrysite [ $result[$x][4] ] = 1; }
       }
      unset ( $message           );
      unset ( $search_words_temp );
      unset ( $term_array        );
      unset ( $terms             );
      unset ( $altterms          );
      unset ( $vars              );
      //------------------------------------------------------------------
      // if ip-address found and timestamp <= timestamp+recount_time
      if ( ( array_key_exists ( $result [ $x ] [ 1 ] , $visitor ) )  && ( $result [ $x ] [ 0 ] <= $visitor [ $result [ $x ] [ 1 ] ] ) )
       {  }
      else
       {
        //------------------------------------------------------------------
        $visitor [ $result [ $x ] [ 1 ] ] = $result [ $x ] [ 0 ] + ( $ip_recount_time * 60 );
        if ( isset ( $visitor_hour [ date ( "H:00" , $result[$x][0] ) ] ) ) { $visitor_hour [ date ( "H:00" , $result[$x][0] ) ]++; } else { $visitor_hour [ date ( "H:00" , $result[$x][0] ) ] = 1; }
        if ( isset ( $visitor_day [ date ( "y/m/d" , $result[$x][0] ) ] ) ) { $visitor_day [ date ( "y/m/d" , $result[$x][0] ) ]++; } else { $visitor_day [ date ( "y/m/d" , $result[$x][0] ) ] = 1; }
        //-------------
        $temp_weekday = date ( "w" , $result[$x][0] );
        if ( $temp_weekday == 0 ) { if ( isset ( $visitor_weekday  [ "0" ] ) ) { $visitor_weekday  [ "0" ]++; } else { $visitor_weekday  [ "0" ] = 1; } }
        if ( $temp_weekday == 1 ) { if ( isset ( $visitor_weekday  [ "1" ] ) ) { $visitor_weekday  [ "1" ]++; } else { $visitor_weekday  [ "1" ] = 1; } }
        if ( $temp_weekday == 2 ) { if ( isset ( $visitor_weekday  [ "2" ] ) ) { $visitor_weekday  [ "2" ]++; } else { $visitor_weekday  [ "2" ] = 1; } }
        if ( $temp_weekday == 3 ) { if ( isset ( $visitor_weekday  [ "3" ] ) ) { $visitor_weekday  [ "3" ]++; } else { $visitor_weekday  [ "3" ] = 1; } }
        if ( $temp_weekday == 4 ) { if ( isset ( $visitor_weekday  [ "4" ] ) ) { $visitor_weekday  [ "4" ]++; } else { $visitor_weekday  [ "4" ] = 1; } }
        if ( $temp_weekday == 5 ) { if ( isset ( $visitor_weekday  [ "5" ] ) ) { $visitor_weekday  [ "5" ]++; } else { $visitor_weekday  [ "5" ] = 1; } }
        if ( $temp_weekday == 6 ) { if ( isset ( $visitor_weekday  [ "6" ] ) ) { $visitor_weekday  [ "6" ]++; } else { $visitor_weekday  [ "6" ] = 1; } }
        unset ( $temp_weekday );
        //-------------
        if ( isset ( $visitor_month [ date ( "Y/m" , $result[$x][0] ) ] ) ) { $visitor_month [ date ( "Y/m" , $result[$x][0] ) ]++; } else { $visitor_month [ date ( "Y/m" , $result[$x][0] ) ] = 1; }
        if ( isset ( $visitor_year  [ date ( " Y " , $result[$x][0] ) ] ) ) { $visitor_year  [ date ( " Y " , $result[$x][0] ) ]++; } else { $visitor_year  [ date ( " Y " , $result[$x][0] ) ] = 1; }
        if ( isset ( $browser          [ $pattern_browser          [ $result[$x][2] ] ] ) ) { $browser          [ $pattern_browser          [ $result[$x][2] ] ]++; } else { $browser          [ $pattern_browser          [ $result[$x][2] ] ] = 1; }
        if ( isset ( $operating_system [ $pattern_operating_system [ $result[$x][3] ] ] ) ) { $operating_system [ $pattern_operating_system [ $result[$x][3] ] ]++; } else { $operating_system [ $pattern_operating_system [ $result[$x][3] ] ] = 1; }
        if ( isset ( $resolution       [ $pattern_resolution       [ $result[$x][6] ] ] ) ) { $resolution       [ $pattern_resolution       [ $result[$x][6] ] ]++; } else { $resolution       [ $pattern_resolution       [ $result[$x][6] ] ] = 1; }
        //-------------
        if ( trim ( $result[$x][8] ) != "unknown" )
         {
          if ( isset ( $country [ $result[$x][8] ] ) ) { $country [ $result[$x][8] ]++; } else { $country [ $result[$x][8] ] = 1; }
         }
        else
         {
          if ( isset ( $country [ "unknown" ] ) ) { $country [ "unknown" ]++; } else { $country [ "unknown" ] = 1; }
         }
        //-------------
        if ( $result[$x][7] == "32" ) { if ( isset ( $color_depth [ $lang_colordepth [ 3 ] ] ) ) { $color_depth [ $lang_colordepth [ 3 ] ]++; } else { $color_depth [ $lang_colordepth [ 3 ] ] = 1; } }
        if ( $result[$x][7] == "16" ) { if ( isset ( $color_depth [ $lang_colordepth [ 4 ] ] ) ) { $color_depth [ $lang_colordepth [ 4 ] ]++; } else { $color_depth [ $lang_colordepth [ 4 ] ] = 1; } }
        if ( $result[$x][7] == "24" ) { if ( isset ( $color_depth [ $lang_colordepth [ 5 ] ] ) ) { $color_depth [ $lang_colordepth [ 5 ] ]++; } else { $color_depth [ $lang_colordepth [ 5 ] ] = 1; } }
        if ( $result[$x][7] == "8"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 6 ] ] ) ) { $color_depth [ $lang_colordepth [ 6 ] ]++; } else { $color_depth [ $lang_colordepth [ 6 ] ] = 1; } }
        if ( $result[$x][7] == "4"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 7 ] ] ) ) { $color_depth [ $lang_colordepth [ 7 ] ]++; } else { $color_depth [ $lang_colordepth [ 7 ] ] = 1; } }
        if ( $result[$x][7] == "2"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 8 ] ] ) ) { $color_depth [ $lang_colordepth [ 8 ] ]++; } else { $color_depth [ $lang_colordepth [ 8 ] ] = 1; } }
        if ( $result[$x][7] == "0"  ) { if ( isset ( $color_depth [ $lang_module [ 3 ] ] ) ) { $color_depth [ $lang_module [ 3 ] ]++; } else { $color_depth [ $lang_module [ 3 ] ] = 1; } }
        //------------------------------------------------------------------
       }
       //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
   }
  //----------------------------------------------------------------------------
 }
else
 {
  //----------------------------------------------------------------------------
  // get the data from logfile
  $loader_counter_now = 0; // set the amount of lines read to zero

  $logfile = fopen ( $logfile_choosed , "rb" ); // open logfile
  @fseek ( $logfile , (int) $cache_memory_address );
  while ( !FEOF ( $logfile ) && ( $loader_counter_now <= $creator_number ) ) // as long as there are entries
   {
    //------------------------------------------------------------------
    $logfile_entry = fgetcsv ( $logfile , 60000 , "|" ); // read entry from logfile

    if ( ( isset ( $logfile_entry [ 0 ] ) ) && ( trim ( $logfile_entry [ 0 ] ) >= trim ( $until_timestamp ) ) )
     {
      $loader_finished = 1;
     }

    if ( ( isset ( $logfile_entry [ 0 ] ) ) && ( $logfile_entry [ 0 ] > $cache_time_stamp ) && ( $loader_counter_now <= $creator_number ) && ( $logfile_entry [ 0 ] >= $from_timestamp ) && ( $logfile_entry [ 0 ] <= $until_timestamp ) )
     {
      //------------------------------------------------------------------
      $loader_counter_now++;                               // increase the amount of lines count
      $write_cache = 1;                                    // if new entries are found
      $last_logfile_entry  = $logfile_entry [ 0 ];         // last logfile entry for the cache
      $last_memory_address = ftell ( $logfile );           // last physical address of the last read entry for the cache

      // count site_name without timestamp
      if ( isset ( $site_name [ $logfile_entry [ 4 ] ] ) ) { $site_name [ $logfile_entry [ 4 ] ]++; } else { $site_name [ $logfile_entry [ 4 ] ] = 1; }

      if ( !isset ( $pattern_referer [ $logfile_entry [ 5 ] ] ) ) { $pattern_referer [ $logfile_entry [ 5 ] ] = null; }
      $temp_referer = $pattern_referer [ $logfile_entry [ 5 ] ];
      //-----------------------
      $exception_domain_found = 0;
      if ( !empty ( $temp_referer ) )
       {
        foreach ( $exception_domain as $value )
         {
          if ( strpos ( substr ( $temp_referer , 0 , strpos ( $temp_referer."/" , "/" , 7 ) ) , $value ) !== FALSE )
           {
            $exception_domain_found = 1;
           }
         }
       }
      //-----------------------
      if ( ( trim ( $logfile_entry [ 5 ] ) != "" ) && ( $exception_domain_found == 0 ) ) // if there is no referer and the referer is not found in the exception domain array
       {
        //---------------------------
        $exception_domain_found = 0; // set back that it is an internal referer
        //---------------------------
        // get search engine
        $searchengine = get_search_terms ( $temp_referer );
        //---------------------------------------------------------------------
        if ( trim ( $searchengine ) != "" )
         {
          if ( isset ( $searchengines_archive [ $searchengine ] ) ) { $searchengines_archive [ $searchengine ]++; }
          else { $searchengines_archive [ $searchengine ] = 1; }
         }
        if ( trim ( $search_words_temp ) != "" )
         {
          if ( isset ( $searchwords_archive [ trim ( $search_words_temp ) ] ) ) { $searchwords_archive [ trim ( $search_words_temp ) ]++; }
          else { $searchwords_archive [ trim ( $search_words_temp ) ] = 1; }
         }
        //---------------------------------------------------------------------
        if ( $show_detailed_referer == 0 )
         {
          $referer [ substr ( $temp_referer , 0 , strpos ( substr ( $temp_referer , 7 ) , "/" ) + 7 ) ]++;
         }
        else
         {
          $special_referer_url = $temp_referer;
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
            $special_referer_temp_check_name = dirname ( $special_referer_url )."/".$special_referer_temp_name."?".substr ( $special_referer_temp_check_name , 0 , strlen ( $special_referer_temp_check_name ) - 1 );
           }
          if ( $special_referer_temp_check_name_value == 0 )
           {
            $special_referer_temp_check_name = $temp_referer;
           }

          if ( isset ( $referer [ $special_referer_temp_check_name ] ) )
           {
            $referer [ $special_referer_temp_check_name ]++; // count referer without timestamp
           }
          else
           {
            $referer [ $special_referer_temp_check_name ] = 1;
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
        // save the entry site
        if ( isset ( $entrysite [ $logfile_entry [ 4 ] ] ) ) { $entrysite [ $logfile_entry [ 4 ] ]++; } else { $entrysite [ $logfile_entry [ 4 ] ] = 1; }
       }
      unset ( $temp_referer      );
      unset ( $message           );
      unset ( $search_words_temp );
      unset ( $term_array        );
      unset ( $terms             );
      unset ( $altterms          );
      unset ( $vars              );
      //------------------------------------------------------------------
      // if ip-address found and timestamp <= timestamp+recount_time
      if ( ( array_key_exists ( $logfile_entry [ 1 ] , $visitor ) )  && ( $logfile_entry [ 0 ]  <= $visitor [ $logfile_entry [ 1 ] ]  ) )
       {  }
      else
       {
        //------------------------------------------------------------------
        $visitor [ $logfile_entry [ 1 ] ] = $logfile_entry [ 0 ] + ( $ip_recount_time * 60 );
        if ( isset ( $visitor_hour [ date ( "H:00" , $logfile_entry [ 0 ] ) ] ) ) { $visitor_hour [ date ( "H:00" , $logfile_entry [ 0 ] ) ]++; } else { $visitor_hour [ date ( "H:00" , $logfile_entry [ 0 ] ) ] = 1; }
        if ( isset ( $visitor_day [ date ( "y/m/d" , $logfile_entry [ 0 ] ) ] ) ) { $visitor_day [ date ( "y/m/d" , $logfile_entry [ 0 ] ) ]++; } else { $visitor_day [ date ( "y/m/d" , $logfile_entry [ 0 ] ) ] = 1; }
        //-------------
        $temp_weekday = date ( "w" , $logfile_entry  [ 0 ] );
        if ( $temp_weekday == 0 ) { if ( isset ( $visitor_weekday  [ "0" ] ) ) { $visitor_weekday  [ "0" ]++; } else { $visitor_weekday  [ "0" ] = 1; } }
        if ( $temp_weekday == 1 ) { if ( isset ( $visitor_weekday  [ "1" ] ) ) { $visitor_weekday  [ "1" ]++; } else { $visitor_weekday  [ "1" ] = 1; } }
        if ( $temp_weekday == 2 ) { if ( isset ( $visitor_weekday  [ "2" ] ) ) { $visitor_weekday  [ "2" ]++; } else { $visitor_weekday  [ "2" ] = 1; } }
        if ( $temp_weekday == 3 ) { if ( isset ( $visitor_weekday  [ "3" ] ) ) { $visitor_weekday  [ "3" ]++; } else { $visitor_weekday  [ "3" ] = 1; } }
        if ( $temp_weekday == 4 ) { if ( isset ( $visitor_weekday  [ "4" ] ) ) { $visitor_weekday  [ "4" ]++; } else { $visitor_weekday  [ "4" ] = 1; } }
        if ( $temp_weekday == 5 ) { if ( isset ( $visitor_weekday  [ "5" ] ) ) { $visitor_weekday  [ "5" ]++; } else { $visitor_weekday  [ "5" ] = 1; } }
        if ( $temp_weekday == 6 ) { if ( isset ( $visitor_weekday  [ "6" ] ) ) { $visitor_weekday  [ "6" ]++; } else { $visitor_weekday  [ "6" ] = 1; } }
        unset ( $temp_weekday );
        //-------------
        if ( isset ( $visitor_month [ date ( "Y/m" , $logfile_entry [ 0 ] ) ] ) ) { $visitor_month [ date ( "Y/m" , $logfile_entry [ 0 ] ) ]++; } else { $visitor_month [ date ( "Y/m" , $logfile_entry [ 0 ] ) ] = 1; }
        if ( isset ( $visitor_year  [ date ( " Y " , $logfile_entry [ 0 ] ) ] ) ) { $visitor_year  [ date ( " Y " , $logfile_entry [ 0 ] ) ]++; } else { $visitor_year  [ date ( " Y " , $logfile_entry [ 0 ] ) ] = 1; }
        if ( isset ( $browser          [ $pattern_browser          [ $logfile_entry [ 2 ] ] ] ) ) { $browser          [ $pattern_browser          [ $logfile_entry [ 2 ] ] ]++; } else { $browser          [ $pattern_browser          [ $logfile_entry [ 2 ] ] ] = 1; }
        if ( isset ( $operating_system [ $pattern_operating_system [ $logfile_entry [ 3 ] ] ] ) ) { $operating_system [ $pattern_operating_system [ $logfile_entry [ 3 ] ] ]++; } else { $operating_system [ $pattern_operating_system [ $logfile_entry [ 3 ] ] ] = 1; }
        if ( isset ( $resolution       [ $pattern_resolution       [ $logfile_entry [ 6 ] ] ] ) ) { $resolution       [ $pattern_resolution       [ $logfile_entry [ 6 ] ] ]++; } else { $resolution       [ $pattern_resolution       [ $logfile_entry [ 6 ] ] ] = 1; }
        //-------------
        if ( trim ( $logfile_entry [ 8 ] ) != "" )
         {
          if ( isset ( $country [ strtolower ( $logfile_entry [ 8 ] ) ] ) ) { $country [ strtolower ( $logfile_entry [ 8 ] ) ]++; } else { $country [ strtolower ( $logfile_entry [ 8 ] ) ] = 1; }
         }
        else
         {
          if ( isset ( $country [ "unknown" ] ) ) { $country [ "unknown" ]++; } else { $country [ "unknown" ] = 1; }
         }
        //-------------
        if ( $logfile_entry [ 7 ] == "32" ) { if ( isset ( $color_depth [ $lang_colordepth [ 3 ] ] ) ) { $color_depth [ $lang_colordepth [ 3 ] ]++; } else { $color_depth [ $lang_colordepth [ 3 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == "16" ) { if ( isset ( $color_depth [ $lang_colordepth [ 4 ] ] ) ) { $color_depth [ $lang_colordepth [ 4 ] ]++; } else { $color_depth [ $lang_colordepth [ 4 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == "24" ) { if ( isset ( $color_depth [ $lang_colordepth [ 5 ] ] ) ) { $color_depth [ $lang_colordepth [ 5 ] ]++; } else { $color_depth [ $lang_colordepth [ 5 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == "8"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 6 ] ] ) ) { $color_depth [ $lang_colordepth [ 6 ] ]++; } else { $color_depth [ $lang_colordepth [ 6 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == "4"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 7 ] ] ) ) { $color_depth [ $lang_colordepth [ 7 ] ]++; } else { $color_depth [ $lang_colordepth [ 7 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == "2"  ) { if ( isset ( $color_depth [ $lang_colordepth [ 8 ] ] ) ) { $color_depth [ $lang_colordepth [ 8 ] ]++; } else { $color_depth [ $lang_colordepth [ 8 ] ] = 1; } }
        if ( $logfile_entry [ 7 ] == ""   ) { if ( isset ( $color_depth [ $lang_module [ 3 ] ] ) ) { $color_depth [ $lang_module [ 3 ] ]++; } else { $color_depth [ $lang_module [ 3 ] ] = 1; } }
        //------------------------------------------------------------------
       }
      //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
   }
  if ( FEOF ( $logfile ) )
   {
    $loader_finished = 1;
   }

  fclose ( $logfile ); // close logfile
  unset  ( $logfile );
  //----------------------------------------------------------------------------
 }
################################################################################
### save the last logfile entry of the timestamp ###
if ( $write_cache == 1 )
 {
  if ( isset ( $_GET [ 'archive' ] ) )
   {
    $cache_time_stamp_file = fopen ( "log/cache_time_stamp_archive.php" , "w+" );
   }
  else
   {
    //------------------------------------------------------------------
    // save the last read physical address of the last entry of the read entry
    if ( $db_active != 1 )
     {
      //------------------------------------------------------------------
      $cache_time_stamp_file = fopen ( "log/cache_memory_address.php" , "r+" );
      flock ( $cache_time_stamp_file , LOCK_EX );
       ftruncate ( $cache_time_stamp_file , 0 );
       fwrite ( $cache_time_stamp_file , "<?php \$cache_memory_address = \"".$last_memory_address."\";?>" ); // save the last read physical address of the logfile
      flock ( $cache_time_stamp_file , LOCK_UN );
      fclose ( $cache_time_stamp_file );
      unset  ( $cache_time_stamp_file );
      //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
    $cache_time_stamp_file = fopen ( "log/cache_time_stamp.php" , "r+" );
    //------------------------------------------------------------------
   }

  flock ( $cache_time_stamp_file , LOCK_EX );
   ftruncate ( $cache_time_stamp_file , 0 );
   fwrite ( $cache_time_stamp_file , "<?php \$cache_time_stamp = \"".$last_logfile_entry."\";?>" ); //save the last read timestamp of the logfile
  flock ( $cache_time_stamp_file , LOCK_UN );
  fclose ( $cache_time_stamp_file );
  unset  ( $cache_time_stamp_file );
 }
################################################################################
### get the latest timestamp ###
if ( $db_active == 1 )
 {
  //------------------------------
  $query                = "SELECT MAX(timestamp) FROM ".$db_prefix."_main";
  $result_temp          = db_query ( $query , 1 , 0 );
  $loader_finished_temp = $result_temp[0][0];
  unset ( $result_temp );
  //------------------------------
 }
else
 {
  //------------------------------
  $loader_finished_temp = file_get_contents ( "log/last_timestamp.dta" );
  //------------------------------
 }
################################################################################
// if the last read logfile entry is the last timestamp in the whole logfile, set the output to "finished"
if ( $db_active == 1 )
 {
  //------------------------------
  if ( !isset ( $cache_time_stamp ) ) { $cache_time_stamp = 0; }
  //------------------------------
  if ( isset ( $result[$x][0] ) ) { $entry1 = $result[$x][0]; } else { $entry1 = 999; }
  if ( ( $last_logfile_entry == $loader_finished_temp ) || ( $cache_time_stamp == $loader_finished_temp ) || ( $entry1 == $loader_finished_temp ) )
   {
    $loader_finished = 1;
   }
  //------------------------------
 }
else
 {
  //------------------------------
  if ( !isset ( $cache_time_stamp ) ) { $cache_time_stamp = 0; }
  //------------------------------
  if ( isset ( $logfile_entry [ 0 ] ) ) { $entry1 = $logfile_entry [ 0 ]; } else { $entry1 = 999; }
  if ( ( $last_logfile_entry == $loader_finished_temp ) || ( $cache_time_stamp == $loader_finished_temp ) || ( $entry1 == $loader_finished_temp ) )
   {
    $loader_finished = 1;
   }
  //------------------------------
 }
################################################################################
### save all array entries to the cache file ###
if ( $write_cache == 1 )
 {
  if ( isset ( $_GET [ 'archive' ] ) )
   {
    $cache_visitors_file = fopen ( "log/cache_visitors_archive.php" , "r+" );
   }
  else
   {
    $cache_visitors_file = fopen ( "log/cache_visitors.php" , "r+" );
   }
  flock ( $cache_visitors_file , LOCK_EX );
  ftruncate ( $cache_visitors_file , 0 );
   fwrite ( $cache_visitors_file , "<?php\n" ); // php header
   //----------------
   if ( isset ( $visitor ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$visitor = array ( \n" ); // array header
     $count_array = count ( $visitor );
     foreach ( $visitor as $key => $value )
      {
       if ( $value >= strtotime ( "-2 days" , $cache_time_stamp ) )
        {
         if ( $temp_file_counter == $count_array )
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" );  // array values without ","
          }
         else
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " );  // array values with "," at the end
          }
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" );  // array footer
    }
   //----------------
   if ( isset ( $visitor_hour ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$visitor_hour = array ( \n" ); // array header
     $count_array = count ( $visitor_hour );
     foreach ( $visitor_hour as $key => $value )
      {
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   visitor_day();
   //----------------
   if ( isset ( $visitor_weekday ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$visitor_weekday = array ( \n" ); // array header
     $count_array = count ( $visitor_weekday );
     foreach ( $visitor_weekday as $key => $value )
      {
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   visitor_month();
   visitor_year();
   //----------------
   if ( isset ( $browser ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$browser = array ( \n" ); // array header
     $count_array = count ( $browser );
     foreach ( $browser as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $operating_system ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$operating_system = array ( \n" ); // array header
     $count_array = count ( $operating_system );
     foreach ( $operating_system as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $resolution ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$resolution = array ( \n" ); // array header
     $count_array = count ( $resolution );
     foreach ( $resolution as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $color_depth ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$color_depth = array ( \n" ); // array header
     $count_array = count ( $color_depth );
     foreach ( $color_depth as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $site_name ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$site_name = array ( \n" ); // array header
     $count_array = count ( $site_name );
     foreach ( $site_name as $key => $value )
      {
       $key = kill_special_chars ( strip_tags ( $key ) );
       if ( trim ( $key ) == "---" ) {}
       else
        {
         if ( $temp_file_counter == $count_array )
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
          }
         else
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
          }
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $referer ) )
    {
     //----------------
     // Delete all referer data with just < settings from admin center
     if ( ( $creator_referer_cut != 0 ) && ( count ( $referer ) > 5000 ) )
      {
       asort ( $referer );
       foreach ( $referer as $key => $value )
        {
         if ( $value < ( $creator_referer_cut + 1 ) ) { unset ( $referer [ $key ] ); }
         else { break; }
        }
      }
     //----------------
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$referer = array ( \n" ); // array header
     $count_array = count ( $referer );
     foreach ( $referer as $key => $value )
      {
      $key = kill_special_chars ( strip_tags ( $key ) );
      if ( trim ( $key ) == "---" ) {}
      else
       {
        if ( $temp_file_counter == $count_array )
         {
          fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
         }
        else
         {
          fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
         }
       }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $country ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$country = array ( \n" ); // array header
     $count_array = count ( $country );
     foreach ( $country as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $searchengines_archive ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$searchengines_archive = array ( \n" ); // array header
     $count_array = count ( $searchengines_archive );
     foreach ( $searchengines_archive as $key => $value )
      {
       $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $searchwords_archive ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$searchwords_archive = array ( \n" ); // array header
     $count_array = count ( $searchwords_archive );
     foreach ( $searchwords_archive as $key => $value )
      {
      $key = kill_special_chars ( $key );
       if ( $temp_file_counter == $count_array )
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
        }
       else
        {
         fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   if ( isset ( $entrysite ) )
    {
     $temp_file_counter = 1;
     fwrite ( $cache_visitors_file , "\$entrysite = array ( \n" ); // array header
     $count_array = count ( $entrysite );
     foreach ( $entrysite as $key => $value )
      {
       $key = kill_special_chars ( strip_tags ( $key ) );
       if ( trim ( $key ) == "---" ) {}
       else
        {
         if ( $temp_file_counter == $count_array )
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
          }
         else
          {
           fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
          }
        }
       $temp_file_counter++;
      }
     fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
    }
   //----------------
   fwrite ( $cache_visitors_file , "\n?>" ); // php footer
  flock ( $cache_visitors_file , LOCK_UN );
  fclose ( $cache_visitors_file );
  unset  ( $cache_visitors_file );
  unset  ( $temp_file_counter   );
 }
################################################################################
### cut logfile ###
if ( ( isset ( $loader_finished ) ) && ( $loader_finished == 1 ) )
 {
  //------------------------------------------------------------------
  // set the physical address to zero
  $cache_timestamp_file = fopen ( "log/cache_memory_address.php" , "r+" );
  flock ( $cache_timestamp_file , LOCK_EX );
   ftruncate ( $cache_timestamp_file , 0 );
   fwrite ( $cache_timestamp_file , "<?php \$cache_memory_address = \"\";?>" ); // php header + footer
  flock ( $cache_timestamp_file , LOCK_UN );
  fclose ( $cache_timestamp_file );
  unset  ( $cache_timestamp_file );
  //------------------------------------------------------------------
  if ( ( isset ( $_GET [ 'loadfile' ] ) && ( $_GET [ 'loadfile' ] == 1 ) ) || ( $loggedin == 1 ) )
   {
    if ( $db_active != 1 )
     {
      //------------------------------------------------------------------
      // take all entries of the original logfile that are newer than 2 days to the temp logfile
      $log_file      = fopen ( "log/logdb.dta"      , "r"  ); // open logfile
      $log_file_temp = fopen ( "log/logdb_temp.dta" , "r+" ); // open temp-logfile
      flock ( $log_file_temp , LOCK_EX );
       ftruncate ( $log_file_temp , 0 );
       while ( !FEOF ( $log_file ) )
         {
          $logfile_entry = fgetcsv ( $log_file , 60000 , "|" );   // read entry from logfile
          if ( ( isset ( $logfile_entry [ 0 ] ) ) &&  ( $logfile_entry [ 0 ] >= strtotime ("-2 days" ) ) )
           {
            fwrite ( $log_file_temp , $logfile_entry [ 0 ]."|".$logfile_entry [ 1 ]."|".$logfile_entry [ 2 ]."|".$logfile_entry [ 3 ]."|".$logfile_entry [ 4 ]."|".$logfile_entry [ 5 ]."|".$logfile_entry [ 6 ]."|".$logfile_entry [ 7 ]."|".$logfile_entry [ 8 ]."\n" );
           }
         }
       flock ( $log_file_temp , LOCK_UN );
      fclose ( $log_file          ); // close logfile
      fclose ( $log_file_temp     ); // close logfile
      unset  ( $log_file          ); // kill var
      unset  ( $log_file_temp     ); // kill var

      // kill the original logfile and take all entries from the temp logfile back to the original logfile
      copy ( "log/logdb_temp.dta" , "log/logdb.dta" );
      //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
    if ( $loggedin == 0 )
     {
      //------------------------------------------------------------------
      // set the timestamp for cache update
      $cache_timestamp_file = fopen ( "log/timestamp_cache_update.dta" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , time() );
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //------------------------------------------------------------------
      echo '<script language="javascript"> top.location.replace(\'index.php?parameter=finished\'); </script>';
      //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
   }
  if ( isset ( $_GET [ 'loadfile' ] ) && ( $_GET [ 'loadfile' ] == 2 ) )
   {
     //------------------------------------------------------------------
    if ( isset ( $_GET [ 'archive' ] ) )
     {
      echo '<script language="javascript"> top.location.replace(\'index.php?parameter=finished&archive=1&from_timestamp='.$from_timestamp.'&until_timestamp='.$until_timestamp.'\'); </script>';
     }
    else
     {
      //------------------------------------------------------------------
      // set the timestamp for cache update
      $cache_timestamp_file = fopen ( "log/timestamp_cache_update.dta" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , time() );
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //------------------------------------------------------------------
      echo '<script language="javascript"> top.location.replace(\'config/cache_panel.php?parameter=cache_finished\'); </script>';
      //------------------------------------------------------------------
     }
    //------------------------------------------------------------------
   }
  //------------------------------------------------------------------
 }
else
 {
  if ( $loggedin == 0 )
   {
    if ( isset ( $_GET [ 'loadfile' ] ) && ( $_GET [ 'loadfile' ] == 1 ) )
     {
      //------------------------------------------------------------------
      // set the timestamp for cache update
      $cache_timestamp_file = fopen ( "log/timestamp_cache_update.dta" , "r+" );
      flock ( $cache_timestamp_file , LOCK_EX );
       ftruncate ( $cache_timestamp_file , 0 );
       fwrite ( $cache_timestamp_file , time() );
      flock ( $cache_timestamp_file , LOCK_UN );
      fclose ( $cache_timestamp_file );
      unset  ( $cache_timestamp_file );
      //------------------------------------------------------------------
      echo '<script language="javascript"> location.replace(\'cache_creator.php?loadfile=1\'); </script>';
      exit;
      //------------------------------------------------------------------
     }
    if ( isset ( $_GET [ 'loadfile' ] ) && ( $_GET [ 'loadfile' ] == 2 ) )
     {
      if ( isset ( $_GET [ 'archive' ] ) )
       {
        echo '<script language="javascript"> location.replace(\'cache_creator.php?loadfile=2&archive='.$_GET [ 'archive' ].'\'); </script>';
        exit;
       }
      else
       {
        //------------------------------------------------------------------
        // set the timestamp for cache update
        $cache_timestamp_file = fopen ( "log/timestamp_cache_update.dta" , "r+" );
        flock ( $cache_timestamp_file , LOCK_EX );
         ftruncate ( $cache_timestamp_file , 0 );
         fwrite ( $cache_timestamp_file , time() );
        flock ( $cache_timestamp_file , LOCK_UN );
        fclose ( $cache_timestamp_file );
        unset  ( $cache_timestamp_file );
        //------------------------------------------------------------------
        echo '<script language="javascript">  location.replace(\'cache_creator.php?loadfile=2\'); </script>';
        exit;
        //------------------------------------------------------------------
       }
     }
   }
 }
################################################################################
// copy the updated stat cache into the counter cache file (old solution)
// @copy ( "log/cache_visitors.php" , "log/cache_visitors_counter.php" );

// now new and better way by HR3 (http://www.php-web-statistik.de/cgi-bin/yabb/YaBB.pl?num=1304324086)

if ( ( !isset ( $_GET [ 'archive' ] ) ) && ( $write_cache == 1 ) )
 {
  $cache_visitors_file = fopen ( "log/cache_visitors_counter.php" , "r+" );
  flock ( $cache_visitors_file , LOCK_EX );
   ftruncate ( $cache_visitors_file , 0 );
   fwrite ( $cache_visitors_file , "<?php\n" ); // php header
    visitor_day();
    visitor_month();
    visitor_year();
   fwrite ( $cache_visitors_file , "\n?>" ); // php footer
  flock ( $cache_visitors_file , LOCK_UN );
  fclose ( $cache_visitors_file );
  unset  ( $cache_visitors_file );
 }

function visitor_day()
 {
  global $visitor_day, $cache_visitors_file;
  if ( isset ( $visitor_day ) )
   {
    $temp_file_counter = 1;
    fwrite ( $cache_visitors_file , "\$visitor_day = array ( \n" ); // array header
    $count_array = count ( $visitor_day );
    foreach ( $visitor_day as $key => $value )
     {
      if ( $temp_file_counter == $count_array )
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
       }
      else
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
       }
      $temp_file_counter++;
     }
    fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
   }
  }

function visitor_month()
 {
  global $visitor_month, $cache_visitors_file;
  if ( isset ( $visitor_month ) )
   {
    $temp_file_counter = 1;
    fwrite ( $cache_visitors_file , "\$visitor_month = array ( \n" ); // array header
    $count_array = count ( $visitor_month );
    foreach ( $visitor_month as $key => $value )
     {
      if ( $temp_file_counter == $count_array )
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
       }
      else
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
       }
      $temp_file_counter++;
     }
    fwrite ( $cache_visitors_file , "\n);\n\n" );  // array footer
   }
  }

function visitor_year()
 {
  global $visitor_year, $cache_visitors_file;
  if ( isset ( $visitor_year ) )
   {
    $temp_file_counter = 1;
    fwrite ( $cache_visitors_file , "\$visitor_year = array ( \n" ); // array header
    $count_array = count ( $visitor_year );
    foreach ( $visitor_year as $key => $value )
     {
      if ( $temp_file_counter == $count_array )
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\"\n" ); // array values without ","
       }
      else
       {
        fwrite ( $cache_visitors_file , "\"".$key."\" => \"".$value."\" ,\n " ); // array values with "," at the end
       }
      $temp_file_counter++;
     }
    fwrite ( $cache_visitors_file , "\n);\n\n" ); // array footer
   }
 }
################################################################################
### kill all vars ###
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
unset ( $searchengines_archive );
unset ( $searchwords_archive   );
unset ( $entrysite             );
unset ( $last_logfile_entry    );
unset ( $logfile_entry         );
//------------------------------------------------------------------------------
?>