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
        $hookstable  = $pntables['hooks'];
        $hookscolumn = $pntables['hooks_column'];
        $sql = "UPDATE $hookstable SET $hookscolumn[smodule]= 'Weblinks' WHERE $hookscolumn[smodule] ='Web_Links'";
        $res = DBUtil::executeSQL($sql);
        
        // rename permissions
        $pntables = pnDBGetTables();
        $permcolumn = $pntables['group_perms_column'];
        $where = "WHERE LEFT($permcolumn[component], 10) ='Web_Links'";
        $orderby = "ORDER BY $permcolumn[sequence]";
        $permarray = DBUtil::selectObjectArray('group_perms', $where, $orderby, -1, -1, false);
        $permcount = count($permarray);
        
        for ($cnt=0; $cnt<$permcount; $cnt++) {
            $permarray[$cnt]['component'] = str_replace('Web_Links', 'Weblinks', $permarray[$cnt]['component']);
        }
        DBUtil::updateObjectArray($permarray, 'group_perms', 'pid');
        
        // rename hooks entries
        if (pnModAvailable('EZComments')) {
//            pnModDBInfoLoad('EZComments');
            $pntables = pnDBGetTables();
            $ezccolumn = $pntables['EZComments_column'];
            $where = "WHERE $ezccolumn[modname] = Web_Links";
            $ezcarray = DBUtil::selectObjectArray('ezcomments', $where, '', -1, -1, false);
            $ezccount = count($ezcarray);
            
            for ($cnt=0; $cnt<$ezccount; $cnt++) {
                $ezcarray[$cnt]['modname'] = str_replace('Web_Links', 'Weblinks', $ezcarray[$cnt]['modname']);
            }
            DBUtil::updateObjectArray($ezcarray, 'ezcomments');
        }
        
        if (pnModAvailable('Ratings')) {
//            pnModDBInfoLoad('Ratings');
            $pntables = pnDBGetTables();
            $ratcolumn = $pntables['ratings_column'];
            $where = "WHERE $ratcolumn[module] = Web_Links";
            $ratarray = DBUtil::selectObjectArray('ratings', $where, '', -1, -1, false);
            $ratcount = count($ratarray);
            
            for ($cnt=0; $cnt<$ratcount; $cnt++) {
                $ratarray[$cnt]['module'] = str_replace('Web_Links', 'Weblinks', $ratarray[$cnt]['module']);
            }
            DBUtil::updateObjectArray($ratarray, 'ratings');
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