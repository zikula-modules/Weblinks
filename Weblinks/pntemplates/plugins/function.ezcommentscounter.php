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

function smarty_function_ezcommentscounter($params, &$smarty)
{
    // get the parameters
    extract($params);
    unset($params);

    if (!isset($lid)) {
        $smarty->trigger_error('ezcommentscounter: attribute lid required');
        return false;
    }

    // set default
    $ezcommentscounter = "";

    if (pnModAvailable('EZComments') && pnModIsHooked('EZComments', 'Web_Links')) {
        $items  = pnModAPIFunc('EZComments', 'user', 'getall',
                               array('mod' => 'Web_Links',
                                     'status' => 0,
                                     'objectid' => $lid));

        $ezcommentscounter = count($items);
    }

    if ($ezcommentscounter=="0") $comments = _WL_FEELFREE2ADD;
    elseif ($ezcommentscounter=="1") $comments = "1 "._WL_COMMENT;
    else $comments = "$ezcommentscounter "._WL_COMMENTS;

    if (isset($assign)) {
        $smarty->assign($assign, $comments);
    } else {
        return $comments;
    }
}
?>
