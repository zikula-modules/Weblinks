<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id$
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_countlinks($params, &$smarty)
{
    $allmonthlinks = 0;
    while ($counter < $params['days']) {
        $newlinkdayraw = (time()-(86400 * $counter));
        $newlinkdb = Date("Y-m-d", $newlinkdayraw);
        $pntable =& pnDBGetTables();
        $column = &$pntable['links_links_column'];
        $column2 = &$pntable['links_categories_column'];
        $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";
        $totallinks = DBUtil::selectObjectCount('links_links', $where);
        if ($totallinks) {
            $allmonthlinks = $allmonthlinks + $totallinks;
        }
        $counter++;
    }

    return $allmonthlinks;
}