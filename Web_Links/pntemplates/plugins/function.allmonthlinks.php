<?php
// $Id: function.allmonthlinks.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
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

function smarty_function_allmonthlinks($params, &$smarty)
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    
    $column = &$pntable['links_links_column'];
	$column2 = &$pntable['links_categories_column'];

    if (!isset($allmonthlinks)) {
    $allmonthlinks = 0;
    }
    while ($counter < 30){
        $newlinkdayraw = (time()-(86400 * $counter));
        $newlinkdb = Date("Y-m-d", $newlinkdayraw);
        $totallinks = 0;
        
        $sql = "SELECT $column[cat_id], $column2[title]
                FROM $pntable[links_links], $pntable[links_categories]
                WHERE $column[date] LIKE '%$newlinkdb%'
                AND $column[cat_id]=$column2[cat_id]";

    	$result =& $dbconn->Execute($sql);
    	
        if ($dbconn->ErrorNo() != 0) {
            error_log("DB Error: " . $dbconn->ErrorMsg());
            return false;
        }   	
    	
		while(list($cid, $title)=$result->fields) {
			$result->MoveNext();
        	if (pnSecAuthAction(0, "Web Links::Category", "$title::$cid", ACCESS_READ)) {
           		$totallinks++;
        	}
		}
        $allmonthlinks = $allmonthlinks + $totallinks;
        $counter++;
    }
    
    $result->Close();
    
    return $allmonthlinks;    
}
?>