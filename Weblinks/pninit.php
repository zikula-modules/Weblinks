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
 * init Weblinks module
 */
function Weblinks_init()
{
    //check if an old Web_Links installation is active
    if (pnModAvailable('Web_Links')) {
        // rename Web_Links to Weblinks
        // rename tables
        $tables = array('links_categories' => 'weblinks_categories',
                        'links_links'      => 'weblinks_links',
                        'links_modrequest' => 'weblinks_modrequest',
                        'links_newlink'    => 'weblinks_newlink',
                        'links_editorials' => 'weblinks_editorials',
                        'links_votedata'   => 'weblinks_votedata');
        $dbconn = DBConnectionStack::getConnection();
        $dict   = NewDataDictionary($dbconn);
        $prefix = pnConfigGetVar('prefix');
        foreach ($tables as $oldtable => $newtable) {
            $sqlarray = $dict->RenameTableSQL($prefix.'_'.$oldtable, $prefix.'_'.$newtable);
            $result   = $dict->ExecuteSQLArray($sqlarray);
        }

        // rename modvars
        $oldvars = pnModGetVar('Web_Links');
        if ($oldvars) {
            pnModSetVars('Weblinks', $oldvars);
            pnModDelVar('Web_Links');
        }
        
        // rename hook
        $pntables = pnDBGetTables();
        $hookscolumn = $pntables['hooks_column'];
        $object = array('smodule' => 'Weblinks');
        $where = "WHERE $hookscolumn[smodule] = 'Web_Links'";
        DBUtil::updateObject($object, 'hooks', $where, 'id');  
        
        // rename hooks entries
        if (pnModAvailable('EZComments')) {
            $pntables = pnDBGetTables();
            $ezccolumn = $pntables['EZComments_column'];
            $object = array('modname' => 'Weblinks');
            $where = "WHERE $ezccolumn[modname] = 'Web_Links'";
            DBUtil::updateObject($object, 'ezcomments', $where, 'id');            
        }
        
        if (pnModAvailable('Ratings')) {
            $pntables = pnDBGetTables();
            $ratcolumn = $pntables['ratings_column'];
            $object = array('module' => 'Weblinks');
            $where = "WHERE $ratcolumn[module] = 'Web_Links'";
            DBUtil::updateObject($object, 'ratings', $where, 'rid'); 
        }
    } else {
    
        // Create tables
    
        // creating categories table
        if (!DBUtil::createTable('weblinks_categories')) {
            return false;
        }
    
        // creating links table
        if (!DBUtil::createTable('weblinks_links')) {
            return false;
        }
    
        // creating modrequest table
        if (!DBUtil::createTable('weblinks_modrequest')) {
            return false;
        }
    
        // creating newlink table
        if (!DBUtil::createTable('weblinks_newlink')) {
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
    }
    
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

    if (!DBUtil::dropTable('weblinks_categories')) {
        return false;
    }

    if (!DBUtil::dropTable('weblinks_links')) {
        return false;
    }

    if (!DBUtil::dropTable('weblinks_modrequest')) {
        return false;
    }

    if (!DBUtil::dropTable('weblinks_newlink')) {
        return false;
    }

    // remove module vars
    pnModDelVar('Weblinks');

    // Deletion successful
    return true;
}