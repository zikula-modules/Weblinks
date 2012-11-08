<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_catpath($params, Zikula_View $view)
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if (!isset($params['cid']) || !is_numeric($params['cid'])) {
        return LogUtil::registerArgsError();
    }

    if (!isset($params['linkmyself']) || !is_numeric($params['linkmyself'])) {
        $params['linkmyself'] = 0;
    }

    if (!isset($params['links']) || !is_numeric($params['links'])) {
        $params['links'] = 0;
    }

    if (!isset($params['start']) || !is_numeric($params['start'])) {
        $params['start'] = 0;
    }

    $cat = DBUtil::selectObjectByID('links_categories', $params['cid'], 'cat_id');

    if ($params['linkmyself']) {
        $cpath = "<a href=\"" . DataUtil::formatForDisplay(ModUtil::url('Weblinks', 'user', 'category', array('cid' => $params['cid']))) . "\"> " . DataUtil::formatForDisplay($cat['title']) . " </a>";
    } else {
        $cpath = DataUtil::formatForDisplay($cat['title']);
    }

    for ($v = $cat['parent_id']; $v != 0; $v = $scat['parent_id']) {
        $scat = DBUtil::selectObjectByID('links_categories', $v, 'cat_id');

        if ($params['links']) {
            $cpath = "<a href=\"" . DataUtil::formatForDisplay(ModUtil::url('Weblinks', 'user', 'category', array('cid' => $scat['cat_id']))) . "\"> " . DataUtil::formatForDisplay($scat['title']) . "</a> / $cpath";
        } else {
            $cpath = DataUtil::formatForDisplay($scat['title']) . " / $cpath";
        }
    }

    if ($params['start']) {
        $cpath = "<a href=\"" . DataUtil::formatForDisplay(ModUtil::url('Weblinks', 'user', 'main')) . "\">" . DataUtil::formatForDisplay(__('Start', $dom)) . "</a> / $cpath";
    }

    return $cpath;
}