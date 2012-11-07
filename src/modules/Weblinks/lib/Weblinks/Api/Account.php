<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: pnaccountapi.php 120 2010-09-14 19:40:20Z Petzi-Juist $
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
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // the array that will hold the options
    $items = null;

    // show link for users only
    if(!UserUtil::isLoggedIn()) {
        // not logged in
        return $items;
    }

    $uname = (isset($args['uname'])) ? $args['uname'] : UserUtil::getVar('uname');
    // does this user exist?
    if(UserUtil::getIdFromName($uname) == false) {
        // user does not exist
        return $items;
    }

    // Create an array of links to return
    if(SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_ADD)) {
        $items = array(array('url'    => ModUtil::url('Weblinks', 'user', 'addlink'),
                             'module' => 'core',
                             'set'    => 'icons/large',
                             'title'  => __('Add link', $dom),
                             'icon'   => 'folder_html.gif'));
    }

    // Return the items
    return $items;
}