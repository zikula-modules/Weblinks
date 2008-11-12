<?php
// $Id: function.numrows.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
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
// Purpose of file: Count Weblinks
// ----------------------------------------------------------------------

function smarty_function_numrows($params, &$smarty)
{
    extract($params);
	unset($params);

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$sql = "SELECT COUNT(*) FROM $pntable[links_links]";
	$result =& $dbconn->Execute($sql);
    list($numrows) = $result->fields;

	return $numrows;
}
?>