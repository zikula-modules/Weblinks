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
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if (empty($params['cid']) || empty($params['lid'])) {
        return LogUtil::registerArgsError();
    }

    $linkbottommenu = "";

    if (SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_EDIT)) {
        $linkbottommenu = "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'admin', 'modlink', array('lid' => (int)$params['lid'])))."\">".DataUtil::formatForDisplay(__('Edit this link', $dom))."</a>&nbsp;|&nbsp;";
    } else if (pnModGetVar('Weblinks', 'blockunregmodify') == 1 || SecurityUtil::checkPermission('Weblinks::Category', "::$params[cid]", ACCESS_COMMENT)) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'modifylinkrequest', array('lid' => (int)$params['lid'])))."\">".DataUtil::formatForDisplay(__('Modify', $dom))."</a>&nbsp;|&nbsp;";
    }

    if (pnModGetVar('Weblinks', 'unregbroken') == 1 || SecurityUtil::checkPermission('Weblinks::Category', "::$params[cid]", ACCESS_COMMENT)) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'brokenlink', array('lid' => (int)$params['lid'])))."\">".DataUtil::formatForDisplay(__('Report broken link', $dom))."</a>&nbsp;|&nbsp;";
    }

    if (empty($params['details'])) {
        $linkbottommenu .= "<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'viewlinkdetails', array('lid' => (int)$params['lid'])))."\">".DataUtil::formatForDisplay(__('Details', $dom))."</a>&nbsp;";

        // set default
        $ezcommentscounter = "";

        if (pnModAvailable('EZComments') && pnModIsHooked('EZComments', 'Weblinks')) {
            $items = pnModAPIFunc('EZComments', 'user', 'getall', array('mod' => 'Weblinks', 'status' => 0, 'objectid' => $params['lid']));
            $ezcommentscounter = count($items);

            $linkbottommenu .= "|&nbsp;<a href=\"".DataUtil::formatForDisplay(pnModURL('Weblinks', 'user', 'viewlinkdetails', array('lid' => (int)$params['lid'])))."\">";
            if ($ezcommentscounter=="0") $linkbottommenu .= DataUtil::formatForDisplay(__('Add a comment', $dom))."</a>";
            elseif ($ezcommentscounter=="1") $linkbottommenu .= "1 ".DataUtil::formatForDisplay(__('Comment', $dom))."</a>";
            else $linkbottommenu .= "$ezcommentscounter ".DataUtil::formatForDisplay(__('Comments', $dom))."</a>";
        }
    }

    return $linkbottommenu;
}