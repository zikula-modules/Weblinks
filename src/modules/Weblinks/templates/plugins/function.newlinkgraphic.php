<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_newlinkgraphic($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    echo "&nbsp;";
    $datetime = array();
    preg_match("[([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})]", $params['time'], $datetime);

    $datenow = $datetime[3] . "-" . $datetime[2] . "-" . $datetime[1];

    $startdate = time();
    $count = 0;

    while ($count <= 7) {
        $daysold = DateUtil::getDatetime($startdate, '%d-%m-%Y');

        if ("$daysold" == "$datenow") {
            if ($count <= 1) {
                echo "<img src=\"modules/Weblinks/images/newred.png\" width=\"34\" height=\"15\" alt=\"" . DataUtil::formatForDisplay(__('New today', $dom)) . "\" title=\"" . DataUtil::formatForDisplay(__('New today', $dom)) . "\" />";
            }
            if ($count <= 3 && $count > 1) {
                echo "<img src=\"modules/Weblinks/images/newgreen.png\" width=\"34\" height=\"15\" alt=\"" . DataUtil::formatForDisplay(__('New during last 3 days', $dom)) . "\" title=\"" . DataUtil::formatForDisplay(__('New during last 3 days', $dom)) . "\" />";
            }
            if ($count <= 7 && $count > 3) {
                echo "<img src=\"modules/Weblinks/images/newblue.png\" width=\"34\" height=\"15\" alt=\"" . DataUtil::formatForDisplay(__('New this week', $dom)) . "\" title=\"" . DataUtil::formatForDisplay(__('New this week', $dom)) . "\" />";
            }
        }
        $count++;
        $startdate = (time() - (86400 * $count));
    }
    return;
}