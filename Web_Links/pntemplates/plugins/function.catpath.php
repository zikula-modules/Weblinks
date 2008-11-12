<?php
function smarty_function_catpath($params, &$smarty)
{
    extract($params);
	unset($params);

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
        $cpath = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Web_Links', 'user', 'category', array('cid' => $cid)))."\"> ".DataUtil::formatForDisplay($title)." </a>";
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
            $cpath = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Web_Links', 'user', 'category', array('cid' => $cid)))."\"> ".DataUtil::formatForDisplay($title)."</a> / $cpath";
        } else {
            $cpath = DataUtil::formatForDisplay($title)." / $cpath";
        }
    }
    if ($start) {
      $cpath="<a href=\"".DataUtil::formatForDisplay(pnModURL('Web_Links', 'user', 'main'))."\">"._WL_START."</a> / $cpath";
    }
    return $cpath;
}
?>