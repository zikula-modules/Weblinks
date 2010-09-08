<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_linkbottommenu($params, &$smarty)
{
    if (empty($params['cid']) || empty($params['lid'])) {
        return LogUtil::registerArgsError();
    }
    
    $linkbottommenu = "";

    if (SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_EDIT)) {
        $linkbottommenu = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'admin', 'modlink', array('lid' => (int)$params['lid'])))."\"><img src=\"images/icons/extrasmall/editpaste.gif\" width=\"16\" height=\"16\" alt=\""._WL_EDITTHISLINK."\" title=\""._WL_EDITTHISLINK."\" />&nbsp;</a>";
    }

    if (pnModGetVar('Weblinks', 'blockunregmodify') == 0 || SecurityUtil::checkPermission('Weblinks::Category', "::$params[cid]", ACCESS_COMMENT)) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'modifylinkrequest', array('lid' => (int)$params['lid'])))."\">".pnML("_WL_MODIFY")."</a>&nbsp;|&nbsp;";
    }

    if (SecurityUtil::checkPermission('Weblinks::Category', "::$params[cid]", ACCESS_COMMENT)) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'brokenlink', array('lid' => (int)$params['lid'])))."\">".pnML("_WL_REPORTBROKEN")."</a>&nbsp;|&nbsp;";
    }

    if (empty($params['details'])) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'viewlinkdetails', array('lid' => (int)$params['lid'])))."\">".pnML("_WL_DETAILS")."</a>&nbsp;";

        // set default
        $ezcommentscounter = "";

        if (pnModAvailable('EZComments') && pnModIsHooked('EZComments', 'Weblinks')) {
            $items = pnModAPIFunc('EZComments', 'user', 'getall', array('mod' => 'Weblinks', 'status' => 0, 'objectid' => $params['lid']));
            $ezcommentscounter = count($items);

            $linkbottommenu .= "|&nbsp;<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'viewlinkdetails', array('lid' => (int)$params['lid'])))."\">";
            if ($ezcommentscounter=="0") $linkbottommenu .= pnML("_WL_FEELFREE2ADD")."</a>";
            elseif ($ezcommentscounter=="1") $linkbottommenu .= "1 ".pnML("_WL_COMMENT")."</a>";
            else $linkbottommenu .= "$ezcommentscounter ".pnML("_WL_COMMENTS")."</a>";
        }
    }

    return $linkbottommenu;
}