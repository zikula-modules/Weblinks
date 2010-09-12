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
    $modvars = array('perpage' => 25,
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
                     'linksinblock' => 10);

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
        $modvars = array('perpage' => 25,
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
                         'linksinblock' => 10);

        // set up module variables
        pnModSetVars('Weblinks', $modvars);

        case '2.0':

        // rename Web_Links to Weblinks
/*      // rename tables
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
        }*/

        // rename modvars
        $oldvars = pnModGetVar('Web_Links');
        if ($oldvars) {
            pnModSetVars('Weblinks', $oldvars);
            pnModDelVar('Web_Links');
        }

        // rename hook
        $pntable = pnDBGetTables();
        $hookscolumn = $pntable['hooks_column'];
        $object = array('smodule' => 'Weblinks');
        $where = "WHERE $hookscolumn[smodule] = 'Web_Links'";
        DBUtil::updateObject($object, 'hooks', $where, 'id');

        // rename hooks entries
        if (pnModAvailable('EZComments')) {
            pnModDBInfoLoad('EZComments');
            $pntable = pnDBGetTables();
            $ezccolumn = $pntable['EZComments_column'];
            $where = "WHERE $ezccolumn[modname] = 'Web_Links'";
            $ezcarray = DBUtil::selectObjectArray('EZComments', $where, '', -1, -1, false);
            $ezccount = count($ezcarray);
            for ($cnt=0; $cnt<$ezccount; $cnt++) {
                $ezcarray[$cnt]['modname'] = str_replace('Web_Links', 'Weblinks', $ezcarray[$cnt]['modname']);
            }
             DBUtil::updateObjectArray($ezcarray, 'EZComments', 'id');
        }

        if (pnModAvailable('Ratings')) {
            pnModDBInfoLoad('Ratings');
            $pntable = pnDBGetTables();
            $ratcolumn = $pntable['ratings_column'];
            $where = "WHERE $ratcolumn[module] = 'Web_Links'";
            $ratarray = DBUtil::selectObjectArray('ratings', $where, '', -1, -1, false);
            $ratcount = count($ratarray);
            for ($cnt=0; $cnt<$ratcount; $cnt++) {
                $ratarray[$cnt]['module'] = str_replace('Web_Links', 'Weblinks', $ratarray[$cnt]['module']);
            }
             DBUtil::updateObjectArray($ratarray, 'ratings', 'rid');
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