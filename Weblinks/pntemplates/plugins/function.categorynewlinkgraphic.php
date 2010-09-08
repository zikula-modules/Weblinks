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
    if (!isset($params['cat']) || !is_numeric($params['cat'])){
        return LogUtil::registerArgsError();
    }

    $time = DBUtil::selectObjectByID('links_links', $params['cat'], 'cat_id');

    if (!$time['date']) {
        return;
    } else {
        echo "&nbsp;";
        ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $time['date'], $datetime);

        $datenow = $datetime[3]."-".$datetime[2]."-".$datetime[1];

        $startdate = time();
        $count = 0;

        while ($count <= 7) {
            $daysold = ml_ftime(""._WL_DATESTRING."", $startdate);

            if ("$daysold" == "$datenow") {
                if ($count<=1) {
            echo "<img src=\"modules/Weblinks/pnimages/newred.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTODAY."\" />";
            }
                if ($count<=3 && $count>1) {
            echo "<img src=\"modules/Weblinks/pnimages/newgreen.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWLAST3DAYS."\" />";
            }
                if ($count<=7 && $count>3) {
            echo "<img src=\"modules/Weblinks/pnimages/newblue.gif\" width=\"34\" height=\"15\" alt=\""._WL_NEWTHISWEEK."\" />";
            }
        }
            $count++;
            $startdate = (time()-(86400 * $count));
        }
        return;
    }
}