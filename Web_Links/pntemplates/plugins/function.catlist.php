<?php
// $Id: function.catlist.php,v 1.0 2005/05/23 20:12:22 petzi-juist Exp $
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

function smarty_function_catlist($params, &$smarty)
{
    extract($params);
	unset($params);

    if (!isset($scat) || !is_numeric($scat)){
    	return _MODARGSERROR;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $s="";
    $column = &$pntable['links_categories_column'];
    $result =& $dbconn->Execute("SELECT $column[cat_id] FROM $pntable[links_categories]
                        WHERE $column[parent_id]='".(int)DataUtil::formatForStore($scat)."'
                        ORDER BY $column[title]");
    while(list($cid)=$result->fields) {

    	$result->MoveNext();
    if ($sel==$cid) {
    	$selstr=' selected="selected"';
    } else {
    	$selstr='';
    }
    $s.="<option value=\"$cid\" $selstr>".catpath($cid,0,0,0)."</option>";
    $s.=catlist($cid, $sel);
    }
    return $s;
}
function catpath($cid, $start, $links, $linkmyself) {

    if (!isset($cid) || !is_numeric($cid)){
        return _MODARGSERROR;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_categories_column'];
    $result =& $dbconn->Execute("SELECT $column[parent_id], $column[title] FROM $pntable[links_categories]
                        WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'");
    list($pid, $title)=$result->fields;
    if ($linkmyself) {
        $cpath = "<a href=\"".$GLOBALS['modurl']."&amp;req=viewlink&amp;cid=$cid\"> ".DataUtil::formatForDisplay($title)." </a>";
    } else {
        $cpath = DataUtil::formatForDisplay($title);
    }
    while ($pid!=0) {
        $column = &$pntable['links_categories_column'];
        $result =& $dbconn->Execute("SELECT $column[cat_id], $column[parent_id], $column[title]
                        FROM $pntable[links_categories]
                        WHERE $column[cat_id]='".(int)DataUtil::formatForStore($pid)."'");
        list($cid, $pid, $title)=$result->fields;
        if ($links) {
            $cpath = "<a href=\"".$GLOBALS['modurl']."&amp;req=viewlink&amp;cid=$cid\"> ".DataUtil::formatForDisplay($title)."</a> / $cpath";
        } else {
            $cpath = DataUtil::formatForDisplay($title)." / $cpath";
        }
    }
    if ($start) {
      $cpath="<a href=\"".$GLOBALS['modurl']."\">"._START."</a> / $cpath";
    }
    return $cpath;
}
function catlist($scat, $sel)
{
    if (!isset($scat) || !is_numeric($scat)){
    	return _MODARGSERROR;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $s="";
    $column = &$pntable['links_categories_column'];
    $result =& $dbconn->Execute("SELECT $column[cat_id] FROM $pntable[links_categories]
                        WHERE $column[parent_id]='".(int)DataUtil::formatForStore($scat)."'
                        ORDER BY $column[title]");
    while(list($cid)=$result->fields) {

    	$result->MoveNext();
    if ($sel==$cid) {
    	$selstr=' selected="selected"';
    } else {
    	$selstr='';
    }
    $s.="<option value=\"$cid\" $selstr>".catpath($cid,0,0,0)."</option>";
    $s.=catlist($cid, $sel);
    }
    return $s;
}
?>