<?php
/**
 * Zikula Application Framework
 *
 * Web_Links
 *
 * @version $Id$
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
 
function smarty_function_orderbyTrans($params, &$smarty)
{
    extract($params);
    unset($params);

    if ($orderby == "hitsA") {
    $orderbyTrans = ""._WL_POPULARITY1."";
    }
    if ($orderby == "hitsD") {
    $orderbyTrans = ""._WL_POPULARITY2."";
    }
    if ($orderby == "titleA") {
    $orderbyTrans = ""._WL_TITLEAZ."";
    }
    if ($orderby == "titleD") {
    $orderbyTrans = ""._WL_TITLEZA."";
    }
    if ($orderby == "dateA") {
    $orderbyTrans = ""._WL_DATE1."";
    }
    if ($orderby == "dateD") {
    $orderbyTrans = ""._WL_DATE2."";
    }
    if ($orderby == "ratingA") {
    $orderbyTrans = ""._WL_RATING1."";
    }
    if ($orderby == "ratingD") {
    $orderbyTrans = ""._WL_RATING2."";
    }

    return $orderbyTrans;
}