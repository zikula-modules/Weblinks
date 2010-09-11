<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.allweeklinks.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_allweeklinks($params, &$smarty)
{
    while ($counter < 7) {
        $newlinkdayraw = (time()-(86400 * $counter));
        $newlinkdb = Date("Y-m-d", $newlinkdayraw);
        $pntable =& pnDBGetTables();
        $column = &$pntable['weblinks_links_column'];
        $column2 = &$pntable['weblinks_categories_column'];
        $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";
        $totallinks = DBUtil::selectObjectCount('weblinks_links', $where);
        if ($totallinks) {
            $allmonthlinks = $allmonthlinks + $totallinks;
        }
        $counter++;
    }

    return $allmonthlinks;
}