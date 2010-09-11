<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.catpath.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
 
function smarty_function_catpath($params, &$smarty)
{
    if (!isset($params['cid']) || !is_numeric($params['cid'])){
        return LogUtil::registerArgsError();
    }

    $cat = DBUtil::selectObjectByID('weblinks_categories', $params['cid'], 'cat_id');

    if ($params['linkmyself']) {
        $cpath = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'category', array('cid' => $params['cid'])))."\"> ".DataUtil::formatForDisplay($cat['title'])." </a>";
    } else {
        $cpath = DataUtil::formatForDisplay($cat['title']);
    }

    for ($v = $cat['parent_id']; $v != 0; $v = $scat['parent_id']) {
        $scat = DBUtil::selectObjectByID('weblinks_categories', $v, 'cat_id');

        if ($params['links']) {
            $cpath = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'category', array('cid' => $scat['cat_id'])))."\"> ".DataUtil::formatForDisplay($scat['title'])."</a> / $cpath";
        } else {
            $cpath = DataUtil::formatForDisplay($scat['title'])." / $cpath";
        }
    }

    if ($params['start']) {
      $cpath = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'main'))."\">"._WL_START."</a> / $cpath";
    }

    return $cpath;
}