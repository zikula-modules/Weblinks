<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_countsublinks($params, Zikula_View $view)
{
    if (!isset($params['cid']) || !is_numeric($params['cid'])) {
        return LogUtil::registerArgsError();
    }

    $count = 0;
    $dbtable = DBUtil::getTables();
    $column = $dbtable['links_links_column'];
    $where = "WHERE $column[cat_id]='" . (int)DataUtil::formatForStore($params['cid']) . "'";
    $count = DBUtil::selectObjectCount('links_links', $where);

    // Now get all child nodes
    $column = $dbtable['links_categories_column'];
    $where = "WHERE $column[parent_id]='" . (int)DataUtil::formatForStore($params['cid']) . "'";
    $cat = DBUtil::selectObjectArray('links_categories', $where);


    foreach ($cat as $result) {
        $count += CountSubLinks($result['cat_id']);
    }
    return $count;
}

function CountSubLinks($sid)
{
    $count = 0;
    $dbtable = DBUtil::getTables();
    $column = $dbtable['links_links_column'];
    $where = "WHERE $column[cat_id]='" . (int)DataUtil::formatForStore($sid) . "'";
    $count = DBUtil::selectObjectCount('links_links', $where);

    // Now get all child nodes
    $column = $dbtable['links_categories_column'];
    $where = "WHERE $column[parent_id]='" . (int)DataUtil::formatForStore($sid) . "'";
    $cat = DBUtil::selectObjectArray('links_categories', $where);


    foreach ($cat as $result) {
        $count += CountSubLinks($result['cat_id']);
    }
    return $count;
}