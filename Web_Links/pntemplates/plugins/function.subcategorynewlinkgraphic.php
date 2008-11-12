<?php
function smarty_function_subcategorynewlinkgraphic($params, &$smarty)
{
    extract($params);
	unset($params);

    if (!isset($scatid) || !is_numeric($scatid)){
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    $column = &$pntable['links_links_column'];

	$query = "SELECT $column[date]
		      FROM $pntable[links_links]
			  WHERE $column[cat_id]= '".(int)DataUtil::formatForStore($scatid)."'
			  ORDER BY $column[date] DESC";
	$newresult = $dbconn->SelectLimit($query, 1);
    list($time)=$newresult->fields;

    if (!$time) return;
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
            echo "<img src=\"modules/Web_Links/pnimages/newred.gif\" width=\"34\" height=\"15\" alt=\""._CATNEWTODAY."\" />&nbsp;";
        }
                if ($count<=3 && $count>1) {
            echo "<img src=\"modules/Web_Links/pnimages/newgreen.gif\" width=\"34\" height=\"15\" alt=\""._CATLAST3DAYS."\" />&nbsp;";
        }
                if ($count<=7 && $count>3) {
            echo "<img src=\"modules/Web_Links/pnimages/newblue.gif\" width=\"34\" height=\"15\" alt=\""._CATTHISWEEK."\" />&nbsp;";
        }
    }
        $count++;
        $startdate = (time()-(86400 * $count));
    }
    return;
}
?>