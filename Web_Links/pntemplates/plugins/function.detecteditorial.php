<?php
// $Id: function.detecteditorial.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
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

function smarty_function_detecteditorial($params, &$smarty)
{
    extract($params);
	unset($params);

    if (!isset($lid) || !is_numeric($lid)){
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    if (!isset($img) || !is_numeric($img)){
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_editorials_column'];
    $result =& $dbconn->Execute("SELECT count(*)
                                 FROM $pntable[links_editorials]
                                 WHERE $column[linkid]='".(int)DataUtil::formatForStore($lid)."'");
    list($recordexist) = $result->fields;
    if ($recordexist != 0) {
        if ($img == 1) {
            echo "&nbsp;&nbsp;<a href=\"".pnVarPrepHTMLDisplay(pnModURL('Web_Links', 'user', 'viewlinkeditorial', array('lid' => $lid)))."\"><img src=\"modules/Web_Links/pnimages/cool.gif\" alt=\""._WL_EDITORIAL."\" /></a>";
        } else {
            echo " | <a href=\"".pnVarPrepHTMLDisplay(pnModURL('Web_Links', 'user', 'viewlinkeditorial', array('lid' => $lid)))."\">"._WL_EDITORIAL."</a>";
        }
    }
    return;
}
?>