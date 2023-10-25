<?php
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     20.0                                                     #
# File-Release-Date:  23/05/06                                                 #
# Official web site and latest version:    http://www.php-web-statistik.de     #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
function last_login_log ( $who )
 {
  //--------------------------------
  include ( "config/config.php" ); // include server time
  //--------------------------------
  $actual_date = time (); // time today
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
    $actual_date += $time_zone_offsets[$server_time];
  }
  //--------------------------------
  $last_login_logfile = fopen ( "log/last_logins.dta" , "a+" );
   fwrite ( $last_login_logfile , $actual_date."|".$_SERVER [ "REMOTE_ADDR" ]."|".$who."\n" );
  fclose ( $last_login_logfile );
  unset  ( $last_login_logfile );
  //--------------------------------
 }
//------------------------------------------------------------------------------
?>