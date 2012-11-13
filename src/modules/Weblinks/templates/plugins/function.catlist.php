<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_catlist($params, Zikula_View $view)
{
    if (!isset($params['scat']) || !is_numeric($params['scat'])) {
        return LogUtil::registerArgsError();
    }
    $em = ServiceUtil::getService('doctrine.entitymanager');

    $categories = $em->getRepository('Weblinks_Entity_Category')->getAll('title', $params['scat']);
    require_once $view->_get_plugin_filepath('function', 'catpath');

    $s = "";
    foreach ($categories as $category) {
        $cat = array('cat_id' => $category->getCat_id());
        if ($params['sel'] == $cat['cat_id']) {
            $selstr = ' selected="selected"';
        } else {
            $selstr = '';
        }
        $catpath = smarty_function_catpath(array('cid' => $cat['cat_id']), $view);
        $s .= "<option value='$cat[cat_id]' $selstr>$catpath</option>";
        $s .= smarty_function_catlist(array('scat' => $cat['cat_id'], 'sel' => $params['sel']), $view);
    }

    return $s;
}

//function catlist($scat, $sel)
//{
//    if (!isset($scat) || !is_numeric($scat)) {
//        return LogUtil::registerArgsError();
//    }
//
//    $dbtable = DBUtil::getTables();
//    $s = "";
//    $column = $dbtable['links_categories_column'];
//    $where = "WHERE $column[parent_id]='" . (int)DataUtil::formatForStore($scat) . "'";
//    $objArray = DBUtil::selectObjectArray('links_categories', $where, 'title');
//
//    foreach ($objArray as $request) {
//        if ($sel == $request['cat_id']) {
//            $selstr = ' selected="selected"';
//        } else {
//            $selstr = '';
//        }
//        $catpath = smarty_function_catpath(array('cid' => $request['cat_id']), $view);
//        $s .= "<option value=\"$request[cat_id]\" $selstr>$catpath</option>";
//        $s .= catlist($request['cat_id'], $sel);
//    }
//
//    return $s;
//}
