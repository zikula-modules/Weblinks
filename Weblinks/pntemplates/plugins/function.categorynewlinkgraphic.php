<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.categorynewlinkgraphic.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_categorynewlinkgraphic($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if (!isset($params['cat']) || !is_numeric($params['cat'])){
        return LogUtil::registerArgsError();
    }
    $pntable = pnDBGetTables();
    $column = &$pntable['links_links_column'];
    $where = "WHERE $column[cat_id]= '".(int)DataUtil::formatForStore($params['cat'])."'";
    $orderby = "ORDER BY $column[date] DESC";
    $time = DBUtil::selectObjectArray('links_links', $where, $orderby, '-1', '1');

    if (!$time['0']['date']) {
        return;
    } else {
        echo "&nbsp;";
        ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $time['0']['date'], $datetime);

        $datenow = $datetime[3]."-".$datetime[2]."-".$datetime[1];

        $startdate = time();
        $count = 0;

        while ($count <= 7) {
            $daysold = DateUtil::getDatetime($startdate, '%d-%m-%Y');

            if ("$daysold" == "$datenow") {
                if ($count<=1) {
                    echo "<img src=\"modules/Weblinks/pnimages/newred.gif\" width=\"34\" height=\"15\" alt=\"".DataUtil::formatForDisplay(__('New today', $dom))."\" title=\"".DataUtil::formatForDisplay(__('New today', $dom))."\" />";
                }
                if ($count<=3 && $count>1) {
                    echo "<img src=\"modules/Weblinks/pnimages/newgreen.gif\" width=\"34\" height=\"15\" alt=\"".DataUtil::formatForDisplay(__('New during last 3 days', $dom))."\" title=\"".DataUtil::formatForDisplay(__('New during last 3 days', $dom))."\" />";
                }
                if ($count<=7 && $count>3) {
                    echo "<img src=\"modules/Weblinks/pnimages/newblue.gif\" width=\"34\" height=\"15\" alt=\"".DataUtil::formatForDisplay(__('New this week', $dom))."\" title=\"".DataUtil::formatForDisplay(__('New this week', $dom))."\" />";
                }
            }
            $count++;
            $startdate = (time()-(86400 * $count));
        }
        return;
    }
}