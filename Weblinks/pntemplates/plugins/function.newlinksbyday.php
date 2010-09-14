<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.newlinksbyday.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_newlinksbyday($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if ( ($params['newlinkshowdays'] != "7" && $params['newlinkshowdays'] != "14" && $params['newlinkshowdays'] != "30") ||
        (!is_numeric($params['newlinkshowdays'])) || (!isset($params['newlinkshowdays'])) ) {
        $params['newlinkshowdays'] = "7";
    }

    while ($counter < $params['newlinkshowdays']) {
        $newlinkdayraw = (time()-(86400 * $counter));
        $newlinkview = date(__('M d, Y', $dom), $newlinkdayraw);
        $newlinkdb = date("Y-m-d", $newlinkdayraw);
        $pntable =& pnDBGetTables();
        $column = &$pntable['links_links_column'];
        $column2 = &$pntable['links_categories_column'];
        $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";
        $totallinks = DBUtil::selectObjectCount('links_links', $where);
        $allweeklinks = $allweeklinks + $totallinks;
        $counter++;
        echo "<a href=\"".pnVarPrepForDisplay(pnModURL('Weblinks', 'user', 'newlinksdate', array('selectdate' => $newlinkdayraw)))."\">".pnVarPrepForDisplay($newlinkview)."</a>&nbsp;(".pnVarPrepForDisplay($totallinks).")<br />";
    }
}