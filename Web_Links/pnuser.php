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

/**
* the main user function
*/
function Web_Links_user_main()
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links');

	// start with the view function
	$pnRender->assign('main', Web_Links_user_view());

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_main.html');
}

/**
* view
*/
function Web_Links_user_view()
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // Permission check for template
    if (SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_ADMIN)) {
        $userpermission = "admin";
    } else if (SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_COMMENT)) {
        $userpermission = "comment";
    }

    // get the main categories
    $categories = pnModAPIFunc('Web_Links', 'user', 'categories');

	// The return value of the function is checked
    if (!$categories) {
        return DataUtil::formatForDisplayHTML(_WL_NOCATS);
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links');

    // assign various useful template variables
	$pnRender->assign('mainlink', 0);
	$pnRender->assign('userpermission', $userpermission);
	$pnRender->assign('categories', $categories);
	$pnRender->assign('subcategories', $categories);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_view.html');
}
?>