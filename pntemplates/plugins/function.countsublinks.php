<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.countsublinks.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_countsublinks($params, &$smarty)
{
    if (!isset($params['cid']) || !is_numeric($params['cid'])){
        return LogUtil::registerArgsError();
    }

    $count = 0;
    $pntable = DBUtil::getTables();
    $column = $pntable['links_links_column'];
    $where = "WHERE $column[cat_id]='".(int)DataUtil::formatForStore($params['cid'])."'";
    $count = DBUtil::selectObjectCount('links_links', $where);

    // Now get all child nodes
    $column = $pntable['links_categories_column'];
    $where = "WHERE $column[parent_id]='".(int)DataUtil::formatForStore($params['cid'])."'";
    $cat = DBUtil::selectObjectArray('links_categories', $where);


    foreach ($cat as $result) {
        $count += CountSubLinks($result['cat_id']);
    }
    return $count;
}

function CountSubLinks($sid)
{
    $count = 0;
    $pntable = DBUtil::getTables();
    $column = $pntable['links_links_column'];
    $where = "WHERE $column[cat_id]='".(int)DataUtil::formatForStore($sid)."'";
    $count = DBUtil::selectObjectCount('links_links', $where);

    // Now get all child nodes
    $column = $pntable['links_categories_column'];
    $where = "WHERE $column[parent_id]='".(int)DataUtil::formatForStore($sid)."'";
    $cat = DBUtil::selectObjectArray('links_categories', $where);


    foreach ($cat as $result) {
        $count += CountSubLinks($result['cat_id']);
    }
    return $count;
}