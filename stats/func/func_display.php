<?php
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     20.0                                                     #
# File-Release-Date:  23/05/06                                                 #
# Official web site and latest version:    https://www.php-web-statistik.de    #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
function display_overview ( $title , &$text1 , &$module1 , &$text2 , &$module2 , &$text3 , &$module3 , &$text4 , &$module4 , &$text5 , &$module5 , &$text6 , $module6 , &$text7 , &$module7 , &$text8 , &$module8, &$text9 , &$module9, &$width )
 {
  include ( "config/config.php" ); // include path to logfile
  //----------------------------------------------------------------------------
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
  //----------------------------------------------------------------------------
  echo '
  <div class="module overview">
    <div class="module-header">'.$title.'</div>
    <div class="module-content">
      <table class="module-table">
      <tr><td class="module-overview-data">'.$text1.'</td><td class="module-overview-hits">'.number_format ( ($module1+$stat_add_visitors) , 0 , "," , "." ).'</td></tr>
      '; if ( $stat_add_visitors != 0 ) { echo '<tr><td class="module-overview-data">'.$text2.'</td><td class="module-overview-hits">'.number_format ( $module2 , 0 , "," , "." ).'</td></tr>'; } echo '
      <tr><td class="module-overview-data"><a href="javascript:void(0)" onclick="quick_archive(\'index.php?action=backtostat&from_timestamp='.date ( "d-m-Y" , $time_stamp_temp ).'&until_timestamp='.date ( "d-m-Y" , $time_stamp_temp ).'\');" title="">'.$text3.'</a></td><td class="module-overview-hits">'.number_format ( $module3 , 0 , "," , "." ).'</td></tr>
      <tr><td class="module-overview-data"><a href="javascript:void(0)" onclick="quick_archive(\'index.php?action=backtostat&from_timestamp='.date ( "d-m-Y" , strtotime ( "-1 day" , $time_stamp_temp ) ).'&until_timestamp='.date ( "d-m-Y" , strtotime ( "-1 day" , $time_stamp_temp ) ).'\');" title="">'.$text4.'</a></td><td class="module-overview-hits">'.number_format ( $module4 , 0 , "," , "." ).'</td></tr>
      <tr><td class="module-overview-data"><a href="javascript:void(0)" onclick="quick_archive(\'index.php?action=backtostat&from_timestamp='.date ( "d-m-Y" , mktime ( 0 , 0 , 0 , date ( "m" , $time_stamp_temp ) , 1 , date ( "Y" , $time_stamp_temp ) ) ).'&until_timestamp='.date ( "d-m-Y" , mktime ( 23 , 59 , 59 , date ( "m" , $time_stamp_temp ) , date ( "t" , $time_stamp_temp ) , date ( "Y" , $time_stamp_temp ) ) ).'\');" title="">'.$text5.'</a></td><td class="module-overview-hits">'.number_format ( $module5 , 0 , "," , "." ).'</td></tr>
      <tr><td class="module-overview-data"><a href="javascript:void(0)" onclick="quick_archive(\'index.php?action=backtostat&from_timestamp='.date ( "d-m-Y" , mktime ( 0 , 0 , 0 , date ( "m" , $time_stamp_temp ) -1 , 1 , date ( "Y" , $time_stamp_temp ) ) ).'&until_timestamp='.date ( "d-m-Y" , mktime ( 23 , 59 , 59 , date ( "m" , $time_stamp_temp ) , ( date ( "d" , $time_stamp_temp ) - date ( "d" , $time_stamp_temp ) ) , date ( "Y" , $time_stamp_temp ) ) ).'\');" title="">'.$text6.'</a></td><td class="module-overview-hits">'.number_format ( $module6 , 0 , "," , "." ).'</td></tr>
      <tr><td class="module-overview-data">'.$text7.'</td><td class="module-overview-hits">'.number_format ( $module7 , 0 , "," , "." ).'</td></tr>
      <tr><td class="module-overview-data">'.$text8.'</td><td class="module-overview-hits">'.number_format ( $module8 , 0 , "," , "." ).'</td></tr>
      </table>
    </div>
    <div class="module-footer">
      <div style="width:60%; float:left; text-align:left">'.$text9.'</div>
      <div style="width:35%; float:right; text-align:right">'.number_format ( $module9 , 0 , "," , "." ).'</div>
      <div style="text-align:center">&nbsp;</div>
    </div>
  </div> <!-- /.module -->
  ';
 }
//------------------------------------------------------------------------------
function display ( $class , $title , &$title2 , &$hits , &$bar , &$module_data , $width , $count , &$unknown , &$value_change , $value_max , $detail_link , $flags , $browser_icons , $os_icons , $year_sort )
 {
  include ( "config/config.php" ); // include path to logfile
  include ( $language           ); // include language vars
  //----------------------------------------------------------------------------
  // weekday locale string
      if ( $language == "language/german.php"     ) { $locale = "de_DE.utf-8"; }
  elseif ( $language == "language/english.php"    ) { $locale = "en_US.utf-8"; }
  elseif ( $language == "language/dutch.php"      ) { $locale = "nl_NL.utf-8"; }
  elseif ( $language == "language/italian.php"    ) { $locale = "it_IT.utf-8"; }
  elseif ( $language == "language/spanish.php"    ) { $locale = "es_ES.utf-8"; }
  elseif ( $language == "language/danish.php"     ) { $locale = "da_DK.utf-8"; }
  elseif ( $language == "language/finnish.php"    ) { $locale = "fi_FI.utf-8"; }
  elseif ( $language == "language/french.php"     ) { $locale = "fr_FR.utf-8"; }
  elseif ( $language == "language/turkish.php"    ) { $locale = "tr_TR.utf-8"; }
  elseif ( $language == "language/portuguese.php" ) { $locale = "pt_PT.utf-8"; }
    else { $locale = "en_US.utf-8"; }
  //----------------------------------------------------------------------------
  $count_all   = count ( $module_data ); // amount of array lines
  if ( $year_sort == 1 ) { krsort ( $module_data ); } // desc sort for years module
  $module_data = array_slice ( $module_data , 0 , $count ); // slice the array, cause you want only see x entries
  if ( $year_sort == 1 ) { ksort ( $module_data ); } // asc sort for years module
  //----------------------------------------------------------------------------
  // start stat module
  echo '<div class="module '.$class.'">
  ';
  //----------------------------------------------------------------------------
  if ( $detail_link == "x" )
   {
    if ( ( isset ( $_GET [ "archive" ] ) ) || ( isset ( $_GET [ "archive_save" ] ) ) )
     {
     	echo '<div class="module-header"><a class="module-link" href="#"><span class="glyphicon glyphicon-chevron-up" title="Top"></span></a>'.$title.'</div>';
     }
    else
     {
     	echo '<div class="module-header">'.$title.'</div>';
     }
   }
  else
   {
    if ( $detail_link == "y" )
     {
      echo '<div class="module-header">'.$title.'</div>';
     }
    else
     {
      if ( $detail_link == "referer"                      ) { $window_size = "width:900 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "site_name"                    ) { $window_size = "width:900 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "pattern_resolution.dta"       ) { $window_size = "width:400 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "pattern_operating_system.dta" ) { $window_size = "width:550 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "pattern_browser.dta"          ) { $window_size = "width:500 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "visitors_per_day"             ) { $window_size = "width:350 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "visitors_per_month"           ) { $window_size = "width:350 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "trends_year"                  ) { $window_size = "width:590 height:200"; $window_title = "Trends"; }
      if ( $detail_link == "country"                      ) { $window_size = "width:600 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "searchwords_archive"          ) { $window_size = "width:800 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "searchengines_archive"        ) { $window_size = "width:600 height:550"; $window_title = "".$lang_detail[2].""; }
      if ( $detail_link == "entrysite"                    ) { $window_size = "width:900 height:550"; $window_title = "".$lang_detail[2].""; }

      if ( $detail_link == "searchwords_archive" )
       {
       	if ( ( isset ( $_GET [ "archive" ] ) ) || ( isset ( $_GET [ "archive_save" ] ) ) )
         {
          if ( ( isset ( $_GET [ 'archive_save' ] ) ) && ( substr ( $_GET [ 'archive_save' ] , 0 , 3 ) == "log" ) )
           {
            echo '<div class="module-header"><a class="module-link" href="#"><span class="glyphicon glyphicon-chevron-up" title="Top"></span></a><a class="module-link floatbox" href="detail_view.php?archive_save='.$_GET [ "archive_save" ].'&detail_logfile=searchwords_archive_special" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[4].'"></span></a><a class="module-link floatbox" href="detail_view.php?archive_save='.$_GET [ "archive_save" ].'&detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[3].'"></span></a>'.$title.'</div>';
           }
          else // if there is  no archive saved, kill the detail
           {
            echo '<div class="module-header"><a class="module-link" href="#"><span class="glyphicon glyphicon-chevron-up" title="Top"></span></a><a class="module-link floatbox" href="detail_view.php?archive=1&detail_logfile=searchwords_archive_special" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[4].'"></span></a><a class="module-link floatbox" href="detail_view.php?archive=1&detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[3].'"></span></a>'.$title.'</div>';
           }
         }
        else
         {
       	  echo '<div class="module-header"><a class="module-link floatbox" href="detail_view.php?detail_logfile=searchwords_archive_special" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[4].'"></span></a><a class="module-link floatbox" href="detail_view.php?detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[3].'"></span></a>'.$title.'</div>';
         }
       }
      else
       {
       	if ( ( isset ( $_GET [ "archive" ] ) ) || ( isset ( $_GET [ "archive_save" ] ) ) )
         {
          if ( ( isset ( $_GET [ 'archive_save' ] ) ) && ( substr ( $_GET [ 'archive_save' ] , 0 , 3 ) == "log" ) )
           {
            echo '<div class="module-header"><a class="module-link" href="#"><span class="glyphicon glyphicon-chevron-up" title="Top"></span></a><a class="module-link floatbox" href="detail_view.php?archive_save='.$_GET [ "archive_save" ].'&detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[1].'"></span></a>'.$title.'</div>';
           }
          else // if there is  no archive saved, kill the detail
           {
            echo '<div class="module-header"><a class="module-link" href="#"><span class="glyphicon glyphicon-chevron-up" title="Top"></span></a><a class="module-link floatbox" href="detail_view.php?archive=1&detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[1].'"></span></a>'.$title.'</div>';
           }
         }
        else
         {
          if ( ( $detail_link == "visitors_per_month" ) || ( $detail_link == "trends_year" ) )
           {
            if ( $detail_link == "visitors_per_month" )
             {
              echo '<div class="module-header"><a class="module-link floatbox" href="detail_view.php?detail_logfile=trends_month" data-fb-options="showItemNumber:false navType:none width:610 height:550" title="Trends"><span class="glyphicon glyphicon-tasks" title="Trends"></span></a><a class="module-link floatbox" href="detail_view.php?detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[1].'"></span></a>'.$title.'</div>';
             }
            if ( $detail_link == "trends_year" )
             {
              echo '<div class="module-header"><a class="module-link floatbox" href="detail_view.php?detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-tasks" title="Trends"></span></a>'.$title.'</div>';
             }
           }
          else
           {
            echo '<div class="module-header"><a class="module-link floatbox" href="detail_view.php?detail_logfile='.$detail_link.'" data-fb-options="showItemNumber:false navType:none '.$window_size.'" title="'.$window_title.'"><span class="glyphicon glyphicon-stats" title="'.$lang_detail[1].'"></span></a>'.$title.'</div>';
           }
         }
       }
     }
   }
  //----------------------------------------------------------------------------
  echo '
  <div class="module-content">
    <table class="module-table">
    <thead>
    <tr><th class="dv_header" style="text-align:left">'.$title2.'</th><th class="dv_header" style="text-align:right; padding-right:8px">'.$hits.'</th><th class="dv_header" style="text-align:center">'.$bar.'</th><th>&nbsp;</th></tr>
    </thead>
    <tbody>'."\n";
    //----------------------------------------------------------------------------
    $max_value = max ( $module_data ); // get the maximum value of the array
    //----------------------------------------------------------------------------
    foreach ( $module_data as $key => $value )
     {
      // if visitor_day module, delete the year and add weekday
      if ( $value_change == 1 )
       {
        setlocale ( LC_TIME, $locale );
        $weekday = ucwords ( strftime( "%a", strtotime ( substr($key,3,2)."/".substr($key,6,2)."/".date("Y") ) ) );

        $saturday = array('Sa','Sat','Za','Sab','Sáb','Lør','Sam.','Cts');
        $sunday   = array('So','Sun','Zo','Dom','Søn','Dim.','Paz');

        if ( in_array ( $weekday, $saturday ) )
         { $weekday = "<span class=\"display_weekday_6_style\">".$weekday."</span>"; }
        if ( in_array ( $weekday, $sunday ) )
         { $weekday = "<span class=\"display_weekday_7_style\">".$weekday."</span>"; }

        $key = substr ( $key , 3 ).", ".$weekday;
       }
      //--------------------------------------
      // if visitor_month module, first the month, then the year
      if ( $value_change == 2 )
       {
        $key = substr ( $key , 5 ).'/'.substr ( $key , 0 , 4 );
       }
      //--------------------------------------
      echo '<tr';if ( $value == $max_value ) { echo ' class="display_max_style"'; } echo '><td class="module-data">';
      if ( ( trim ( $key ) == "" ) || ( $key == "unknown" ) || ( $key == "Unknown" ) || ( $key == "---" ) )
       {
        //--------------------------------------
        if ( ( $show_browser_icons == 1 ) && ( $detail_link == "pattern_browser.dta" ) )
         {
          echo '<img src="images/browser_icons/'.browser_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="">&nbsp;';
         }
        if ( ( $show_browser_icons == 1 ) && ( isset ( $_GET [ "detail_logfile" ] ) && ( $_GET [ "detail_logfile" ] == "pattern_browser.dta" ) ) )
         {
          echo '<img src="images/browser_icons/'.browser_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="">&nbsp;';
         }
        //--------------------------------------
        if ( ( $show_os_icons == 1 ) && ( $detail_link == "pattern_operating_system.dta" ) )
         {
          echo '<img src="images/os_icons/'.os_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="">&nbsp;';
         }
        if ( ( $show_os_icons == 1 ) && ( isset ( $_GET [ "detail_logfile" ] ) && ( $_GET [ "detail_logfile" ] == "pattern_operating_system.dta" ) ) )
         {
          echo '<img src="images/os_icons/'.os_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="">&nbsp;';
         }
        //--------------------------------------
        echo $unknown;
       }
      else
       {
        if ( substr ( $key , 0 , 4 ) == "http" )
         {
          if ( ( strpos ( $key , "google." ) > 0 ) && ( strpos ( $key , "url?q=" ) > 0 ) )
           {
            $key = str_replace ( "url?q=" , "search?q=" , $key );
           }
          // ellipsis & span style for autocut
          echo '<div class="ellipsis"><span><a class="referer" href="'.$key.'" target="_blank">'.$key.'</a></span></div>';
         }
        else
         {
          //--------------------------------------
          if ( ( $show_country_flags == 1 ) && ( $flags == 1 ) )
           {
            if ( ( $key == $lang_module[3] ) || ( $key == "-" ) || ( $key == "invalid ip address." ) )
             {
              echo '<img src="images/country_flags/unknown.png" style="width:20px; height:13px" alt="">&nbsp;';
             }
            else
             {
              echo '<img src="images/country_flags/'.str_replace ( ")" , "" , substr ( strrchr ( $key , "(" ) , 1 ) ).'.png" style="width:20px; height:13px" alt="">&nbsp;';
             }
           }
          //--------------------------------------
          if ( ( $show_browser_icons == 1 ) && ( $browser_icons == 1 ) )
           {
            echo '<img src="images/browser_icons/'.browser_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="" />&nbsp;';
           }
          //--------------------------------------
          if ( ( $show_os_icons == 1 ) && ( $os_icons == 1 ) )
           {
            echo '<img src="images/os_icons/'.os_matching ( $key ).'.png" style="width:14px; height:14px; vertical-align:middle;" alt="">&nbsp;';
           }
          //--------------------------------------
          echo trim ($key);
         }
       }
      echo '</td>';
      //--------------------------------------
      echo '<td class="module-hits">'.number_format ( $value , 0 , "," , "." ).'</td>';
      //--------------------------------------
      if ( ( $value_max == 0 ) || ( $value == 0 ) )
       {
        $howmuch_1 = 0;
        $howmuch_2 = 100;
       }
      else
       {
        if ( $percentbar_max_value_1 == 0 ) { $sum_total = $value_max; }
        if ( $percentbar_max_value_1 == 1 ) { $sum_total = array_sum ( $module_data ); }

        $howmuch_1 = ( int ) round ( $value / $sum_total * 100 );
        $howmuch_2 = ( int ) 100 - ( $value / $sum_total * 100 );

        if ( $percentbar_max_value_2 == 1 )
         {
          $pos_bg_img = ( int ) round ( $value / max ( $module_data ) * 100 - 100 );
          $width_print_img1 = ( int ) round ( $value / max ( $module_data ) * 100 );
          $width_print_img2 = ( int ) 100 - round ( $value / max ( $module_data ) * 100 );
          $pos_print_img2 = ( int ) round ( $value / max ( $module_data ) * 100 + 1 );
         }
        else
         {
          $pos_bg_img = ( int ) round ( $value / $sum_total * 100 - 100 );
          $width_print_img1 = ( int ) round ( $value / $sum_total * 100 );
          $width_print_img2 = ( int ) 100 - round ( $value / $sum_total * 100 );
          $pos_print_img2 = ( int ) round ( $value / $sum_total * 100 + 1 );
         }
       }
      //--------------------------------------
      echo '<td class="module-slidebar">';
      if ( trim ( $howmuch_2 ) == "100" )
       {
        echo '<div class="progress progress-module" style="width:102px"><div class="progress-bar progress-bar-module" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div></div>';
       }
      else
       {
        if ( trim ( $howmuch_2 ) == "0" )
         {
          echo '<div class="progress progress-module" style="width:102px"><div class="progress-bar progress-bar-module" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div></div>';
         }
        else
         {
          echo '<div class="progress progress-module" style="width:102px"><div class="progress-bar progress-bar-module" role="progressbar" aria-valuenow="'.$width_print_img1.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$width_print_img1.'%"></div></div>';
         }
       }
      echo '</td>';
      //--------------------------------------
      echo '<td class="module-percent">'.$howmuch_1.'%</td></tr>'."\n";
     }
    //----------------------------------------------------------------------------
    if ( ( $db_active == 1 ) && ( $detail_link == "pattern_resolution.dta" ) ) { $count_all--; } // kill the first ID from pattern table resolution
    echo '</tbody>
    </table>
  </div> <!-- /.module-content -->';

  if ( $value_max == 0 ) { $site_visits = 0; } else { $site_visits = (int) @round ( $value_max  / array_sum ( $GLOBALS [ "visitor_year" ] ) ); }

  echo '
  <div class="module-footer">
    <div style="width:30%; float:left; text-align:left">('.number_format ( array_sum ( $module_data ) , 0 , "," , "." ).'/'.number_format ( $value_max , 0 , "," , "." ).')</div>
    <div style="width:20%; float:right; text-align:right">('.number_format ( $count , 0 , "," , "." ).'/'.number_format ( $count_all , 0 , "," , "." ).')</div>
    <div style="text-align:center">'; if ( $detail_link == "site_name" ) { echo '&#216; '.$site_visits.' '.$lang_site[1].' / '.$lang_overview[1].''; } else { echo '&nbsp;'; } echo '</div>
  </div>
  </div> <!-- /.module -->';
  //----------------------------------------------------------------------------
 }
//------------------------------------------------------------------------------
?>