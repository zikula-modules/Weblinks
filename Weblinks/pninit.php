<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id$
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * init Weblinks module
 */
function Weblinks_init()
{
    // Create tables

    // creating categories table
    if (!DBUtil::createTable('links_categories')) {
        return false;
    }

    // creating links table
    if (!DBUtil::createTable('links_links')) {
        return false;
    }

    // creating modrequest table
    if (!DBUtil::createTable('links_modrequest')) {
        return false;
    }

    // creating newlink table
    if (!DBUtil::createTable('links_newlink')) {
        return false;
    }

    // Weblinks settings
    // set up config variables
    $modvars = array(
        'perpage' => 25,
        'toplinkspercentrigger' => 0,
        'toplinks' => 25,
        'mostpoplinkspercentrigger' => 0,
        'mostpoplinks' => 25,
        'featurebox' => 1,
        'blockunregmodify' => 0,
        'popular' => 500,
        'newlinks' => 10,
        'bestlinks' => 10,
        'linksresults' => 10,
        'links_anonaddlinklock' => 1,
        'targetblank' => 0,
        'linksinblock' => 10
    );
    
    // set up module variables
    pnModSetVars('Weblinks', $modvars);

    // Initialisation successful
    return true;

}

/**
 * upgrade
 */
function Weblinks_upgrade($oldversion)
{
    // Get database information
    $pntable = pnDBGetTables();

    switch($oldversion) {
        case '1.0':


    // Weblinks settings
    // set up config variables
    $modvars = array(
        'perpage' => 25,
        'toplinkspercentrigger' => 0,
        'toplinks' => 25,
        'mostpoplinkspercentrigger' => 0,
        'mostpoplinks' => 25,
        'featurebox' => 1,
        'blockunregmodify' => 0,
        'popular' => 500,
        'newlinks' => 10,
        'bestlinks' => 10,
        'linksresults' => 10,
        'links_anonaddlinklock' => 1,
        'targetblank' => 0,
        'linksinblock' => 10
    );
    
    // set up module variables
    pnModSetVars('Weblinks', $modvars);

        case '2.0':
        
    // rename Web_Links to Weblinks
    pnModDBInfoLoad('Modules');
    $pntables = pnDBGetTables();
    $modcolumn = $pntables['modules_column'];
    $item = array('name' => 'Weblinks');
    $where = "WHERE $modcolumn[name] = Web_Links";
    
    DBUtil::updateObject($item, 'modules', $where);
    
    // rename modvars
    $modvars = pnModGetVar('Web_Links');
    if ($modvars) {
        pnModSetVars('Weblinks', $modvars);
        pnModDelVar('Web_Links');
    }
    
    // rename Hooks entries
    if (pnModAvailable('EZComments')) {
        pnModDBInfoLoad('EZComments');
        $pntable = pnDBGetTables();
        $ezccolumn = $pntables['EZComments_column'];
        $item = array('modname' => 'Weblinks');
        $where = "WHERE $ezccolumn[modname] = Web_Links";
        
        DBUtil::updateObject($item, 'ezcomments', $where);
    }
    
    if (pnModAvailable('Ratings')) {
        pnModDBInfoLoad('Ratings');
        $pntable = pnDBGetTables();
        $ratcolumn = $pntables['ratings_column'];
        $item = array('module' => 'Weblinks');
        $where = "WHERE $ratcolumn[module] = Web_Links";
        
        DBUtil::updateObject($item, 'ezcomments', $where);
    }
    
        case '2.0.1':

           break;
    }
    // Upgrade successful
    return true;
}

/**
 * delete the Weblinks module
 */
function Weblinks_delete()
{
    // Delete tables

    if (!DBUtil::dropTable('links_categories')) {
        return false;
    }

    if (!DBUtil::dropTable('links_links')) {
        return false;
    }

    if (!DBUtil::dropTable('links_modrequest')) {
        return false;
    }

    if (!DBUtil::dropTable('links_newlink')) {
        return false;
    }

    // remove module vars
    pnModDelVar('Weblinks');

    // Deletion successful
    return true;
}