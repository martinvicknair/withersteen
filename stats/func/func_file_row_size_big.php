<?php
################################################################################
#                           P H P - W E B - S T A T                            #
################################################################################
# This file is part of php-web-stat.                                           #
# Open-Source Statistic Software for Webmasters                                #
# Script-Version:     20.0                                                     #
# File-Release-Date:  23/04/24                                                 #
# Official web site and latest version:    http://www.php-web-statistik.de     #
#==============================================================================#
# Authors: Holger Naves, Reimar Hoven                                          #
# Copyright © 2023 by PHP Web Stat - All Rights Reserved.                      #
################################################################################

//------------------------------------------------------------------------------
function file_row_size_big ($file) {
  $linecount = 0;
  $handle = fopen($file, "r");

  if (!$handle) {
    return false;
  }

  while (!feof($handle)) {
    $linecount += substr_count(fread($handle, 8192), "\n");
  }

  fclose ($handle);
  unset  ($handle);
  return $linecount;
}
//------------------------------------------------------------------------------
?>