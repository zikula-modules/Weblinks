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
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if (version_compare(PN_VERSION_NUM, '1.2.0', '<')) {
        SessionUtil::setVar('errormsg', __('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.', $dom));
        return false;
    }
    
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
                     'newlinks' => 10,
                     'bestlinks' => 10,
                     'linksresults' => 10,
                     'linksinblock' => 10,
                     'popular' => 500,
                     'mostpoplinkspercentrigger' => 0,
                     'mostpoplinks' => 25,
                     'featurebox' => 1,
                     'targetblank' => 0,
                     'doubleurl' => 0,
                     'blockunregmodify' => 0,
                     'links_anonaddlinklock' => 1);

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
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Get database information
    $pntable = pnDBGetTables();

    switch($oldversion) {
        case '1.0':

        // Weblinks settings
        // set up config variables
        $modvars = array('perpage' => 25,
                     'newlinks' => 10,
                     'bestlinks' => 10,
                     'linksresults' => 10,
                     'linksinblock' => 10,
                     'popular' => 500,
                     'mostpoplinkspercentrigger' => 0,
                     'mostpoplinks' => 25,
                     'featurebox' => 1,
                     'targetblank' => 0,
                     'blockunregmodify' => 0,
                     'links_anonaddlinklock' => 1);

        // set up module variables
        pnModSetVars('Weblinks', $modvars);

        case '2.0':

        // rename Web_Links to Weblinks

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

        if (version_compare(PN_VERSION_NUM, '1.2.0', '<')) {
            SessionUtil::setVar('errormsg', __('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.', $dom));
            return false;
        }
        
        pnModSetVar('Weblinks', 'doubleurl', 0);
        
        // remove obsolete module vars
        pnModDelVar('Weblinks', 'toplinks');
        pnModDelVar('Weblinks', 'toplinkspercentrigger');
        
        case '2.1.0':

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