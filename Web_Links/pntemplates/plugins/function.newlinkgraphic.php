<?php
// $Id: function.newlinkgraphic.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

function smarty_function_newlinkgraphic($params, &$smarty)
{
    extract($params);
	unset($params);

    echo "&nbsp;";
    ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $time, $datetime);
    $datetime = ml_ftime(""._WL_DATESTRING."", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
    $datetime = ucfirst($datetime);
    $startdate = time();
    $count = 0;
    while ($count <= 7) {
    	$daysold = ml_ftime(""._WL_DATESTRING."", $startdate);
        if ("$daysold" == "$datetime") {
            if ($count<=1) {
        echo "<img src=\"modules/Web_Links/pnimages/newred.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTODAY."\" />";
        }
            if ($count<=3 && $count>1) {
        echo "<img src=\"modules/Web_Links/pnimages/newgreen.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWLAST3DAYS."\" />";
        }
            if ($count<=7 && $count>3) {
        echo "<img src=\"modules/Web_Links/pnimages/newblue.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTHISWEEK."\" />";
        }
    }
        $count++;
        $startdate = (time()-(86400 * $count));
    }
	return;
}
?>
