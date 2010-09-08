<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id$
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Return an array of items to show in the your account panel
 *
 * @params   uname   string   the user name
 * @return   array   array of items, or false on failure
 */
function Weblinks_accountapi_getall($args)
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
    if(SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_ADD)) {
        pnModLangLoad('Weblinks', 'user');
        $items = array(array('url'     => pnModURL('Weblinks', 'user', 'addlink'),
                             'module' => 'core',
                             'set' => 'icons/large',
                             'title'   => _WL_ADD,
                             'icon'    => 'folder_html.gif'));
    }

    // Return the items
    return $items;
}