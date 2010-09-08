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
    if ($params['orderby'] == "hitsA") {
        $orderbyTrans = ""._WL_POPULARITY1."";
    }
    if ($params['orderby'] == "hitsD") {
        $orderbyTrans = ""._WL_POPULARITY2."";
    }
    if ($params['orderby'] == "titleA") {
        $orderbyTrans = ""._WL_TITLEAZ."";
    }
    if ($params['orderby'] == "titleD") {
        $orderbyTrans = ""._WL_TITLEZA."";
    }
    if ($params['orderby'] == "dateA") {
        $orderbyTrans = ""._WL_DATE1."";
    }
    if ($params['orderby'] == "dateD") {
        $orderbyTrans = ""._WL_DATE2."";
    }

    return $orderbyTrans;
}