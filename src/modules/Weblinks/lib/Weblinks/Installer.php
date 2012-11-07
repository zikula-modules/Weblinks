<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: pninit.php 156 2010-10-06 08:20:11Z Petzi-Juist $
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

    if (version_compare(Zikula_Core::VERSION_NUM, '1.2.0', '<')) {
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
                     'unregbroken' => 0,
                     'blockunregmodify' => 0,
                     'links_anonaddlinklock' => 0,
                     'thumber' => 0,
                     'thumbersize' => 'XL');

    // set up module variables
    ModUtil::setVars('Weblinks', $modvars);

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
    $pntable = DBUtil::getTables();

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
                     'links_anonaddlinklock' => 0);

        // set up module variables
        ModUtil::setVars('Weblinks', $modvars);

        case '2.0':

        // rename Web_Links to Weblinks

        // rename modvars
        $oldvars = ModUtil::getVar('Web_Links');
        if ($oldvars) {
            ModUtil::setVars('Weblinks', $oldvars);
            ModUtil::delVar('Web_Links');
        }

        // rename hook
        $pntable = DBUtil::getTables();
        $hookscolumn = $pntable['hooks_column'];
        $object = array('smodule' => 'Weblinks');
        $where = "WHERE $hookscolumn[smodule] = 'Web_Links'";
        DBUtil::updateObject($object, 'hooks', $where, 'id');

        // rename hooks entries
        if (ModUtil::available('EZComments')) {
            ModUtil::dbInfoLoad('EZComments');
            $pntable = DBUtil::getTables();
            $ezccolumn = $pntable['EZComments_column'];
            $where = "WHERE $ezccolumn[modname] = 'Web_Links'";
            $ezcarray = DBUtil::selectObjectArray('EZComments', $where, '', -1, -1, false);
            $ezccount = count($ezcarray);
            for ($cnt=0; $cnt<$ezccount; $cnt++) {
                $ezcarray[$cnt]['modname'] = str_replace('Web_Links', 'Weblinks', $ezcarray[$cnt]['modname']);
            }
             DBUtil::updateObjectArray($ezcarray, 'EZComments', 'id');
        }

        if (ModUtil::available('Ratings')) {
            ModUtil::dbInfoLoad('Ratings');
            $pntable = DBUtil::getTables();
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

        if (version_compare(Zikula_Core::VERSION_NUM, '1.2.0', '<')) {
            SessionUtil::setVar('errormsg', __('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.', $dom));
            return false;
        }

        ModUtil::setVar('Weblinks', 'doubleurl', 0);
        ModUtil::setVar('Weblinks', 'unregbroken', 0);
        ModUtil::setVar('Weblinks', 'thumber', 0);
        ModUtil::setVar('Weblinks', 'thumbersize', 'XL');

        // remove obsolete module vars
        ModUtil::delVar('Weblinks', 'toplinks');
        ModUtil::delVar('Weblinks', 'toplinkspercentrigger');

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
    ModUtil::delVar('Weblinks');

    // Deletion successful
    return true;
}