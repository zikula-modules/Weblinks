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
 * Return an array of items to show in the your account panel
 *
 * @params   uname   string   the user name
 * @return   array   array of items, or false on failure
 */
function Web_Links_accountapi_getall($args)
{
    // the array that will hold the options
    $items = null;

    // show link for users only
    if(!pnUserLoggedIn()) {
        // not logged in
        return $items;
    }

    $uname = (isset($args['uname'])) ? $args['uname'] : pnUserGetVar('uname');
    // does this user exist?
    if(pnUserGetIDFromName($uname)==false) {
        // user does not exist
        return $items;
    }

    // Create an array of links to return
    if(SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_COMMENT)) {
        pnModLangLoad('Web_Links', 'user');
        $items = array(array('url'     => pnModURL('Web_Links', 'user', 'addlink'),
                             'module' => 'core',
                             'set' => 'icons/large',
                             'title'   => _WL_ADD,
                             'icon'    => 'folder_html.gif'));
    }

    // Return the items
    return $items;
}