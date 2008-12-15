<?php
/**
 * Zikula Application Framework
 *
 * Web_Links
 *
 * @version $Id$
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_newlinkgraphic($params, &$smarty)
{
    extract($params);
    unset($params);

    echo "&nbsp;";
    ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $time, $datetime);

    $datenow = $datetime[3]."-".$datetime[2]."-".$datetime[1];

    $startdate = time();
    $count = 0;

    while ($count <= 7) {
        $daysold = ml_ftime(""._WL_DATESTRING."", $startdate);

        if ("$daysold" == "$datenow") {
            if ($count<=1) {
        echo "<img src=\"modules/Web_Links/pnimages/newred.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTODAY."\" />";
        }
            if ($count<=3 && $count>1) {
        echo "<img src=\"modules/Web_Links/pnimages/newgreen.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWLAST3DAYS."\" />";
        }
            if ($count<=7 && $count>3) {
        echo "<img src=\"modules/Web_Links/pnimages/newblue.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTHISWEEK."\" />";
        }
    }
        $count++;
        $startdate = (time()-(86400 * $count));
    }
    return;
}