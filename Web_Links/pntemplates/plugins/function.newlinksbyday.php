<?php
// $Id: function.newlinksbyday.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
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
// Original Author of file: Petzi-Juist
// Purpose of file: Count Categories
// ----------------------------------------------------------------------

function smarty_function_newlinksbyday($params, &$smarty)
{
    extract($params);
	unset($params);

	if ( ($newlinkshowdays != "7" && $newlinkshowdays != "14" && $newlinkshowdays != "30") ||
		(!is_numeric($newlinkshowdays)) || (!isset($newlinkshowdays)) ) {
		$newlinkshowdays = "7";
	}

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
	$column2 = &$pntable['links_categories_column'];

    $counter = 0;
    $allweeklinks = 0;
    while ($counter <= $newlinkshowdays-1) {
        $newlinkdayRaw = (time()-(86400 * $counter));
        $newlinkday = date("d-M-Y", $newlinkdayRaw);
        $newlinkView = ml_ftime(_WL_DATEBRIEF, $newlinkdayRaw);
        $newlinkDB = Date("Y-m-d", $newlinkdayRaw);
		$totallinks = 0;

    	$result =& $dbconn->Execute("SELECT $column[cat_id]
									FROM $pntable[links_links]
									WHERE $column[date] LIKE '%$newlinkDB%'
									AND $column[cat_id]=$column2[cat_id]");
		while(list($cid, $title)=$result->fields) {
        	$result->MoveNext();
        	if (pnSecAuthAction(0, "Web Links::Category", "$title::$cid", ACCESS_READ)) {
           		$totallinks++;
        	}
      	}
        $counter++;
        $allweeklinks = $allweeklinks + $totallinks;
        echo "<strong><big>&middot;</big></strong> <a href=\"".pnVarPrepForDisplay(pnModURL('Web_Links', 'user', 'newlinksdate', array('selectdate' => $newlinkdayRaw)))."\">".pnVarPrepForDisplay($newlinkView)."</a>&nbsp;(".pnVarPrepForDisplay($totallinks).")<br />";
    }
    $counter = 0;
    $allmonthlinks = 0;
}
?>