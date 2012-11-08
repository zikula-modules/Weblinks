<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_newlinksbyday($params, &$smarty)
{
    if (($params['newlinkshowdays'] != "7" && $params['newlinkshowdays'] != "14" && $params['newlinkshowdays'] != "30") ||
            (!is_numeric($params['newlinkshowdays'])) || (!isset($params['newlinkshowdays']))) {
        $params['newlinkshowdays'] = "7";
    }

    $counter = 0;
    $totallinks = 0;

    while ($counter < $params['newlinkshowdays']) {
        $newlinkdayraw = (time() - (86400 * $counter));
        $newlinkview = DateUtil::getDatetime($newlinkdayraw, 'datebrief');
        $newlinkdb = DateUtil::transformInternalDate($newlinkdayraw);
        $dbtable = DBUtil::getTables();
        $column = $dbtable['links_links_column'];
        $column2 = $dbtable['links_categories_column'];
        $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";
        $countlinks = DBUtil::selectObjectCount('links_links', $where);
        $totallinks = $totallinks + $countlinks;
        $counter++;
        echo "<a href=\"" . DataUtil::formatForDisplay(ModUtil::url('Weblinks', 'user', 'newlinksdate', array('selectdate' => $newlinkdayraw))) . "\">" . DataUtil::formatForDisplay($newlinkview) . "</a>&nbsp;(" . DataUtil::formatForDisplay($countlinks) . ")<br />";
    }
}