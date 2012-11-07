<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.orderbyTrans.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_orderbyTrans($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if ($params['orderby'] == "hitsA") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Popularity (from fewest hits to most hits)', $dom));
    }
    if ($params['orderby'] == "hitsD") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Popularity (from most hits to fewest hits)', $dom));
    }
    if ($params['orderby'] == "titleA") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Title (A to Z)', $dom));
    }
    if ($params['orderby'] == "titleD") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Title (Z to A)', $dom));
    }
    if ($params['orderby'] == "dateA") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Date (oldest links listed first)', $dom));
    }
    if ($params['orderby'] == "dateD") {
        $orderbyTrans = DataUtil::formatForDisplay(__('Date (newest links listed first)', $dom));
    }

    return $orderbyTrans;
}