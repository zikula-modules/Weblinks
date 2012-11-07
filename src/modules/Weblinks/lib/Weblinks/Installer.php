<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Installer extends Zikula_Installer {

    /**
    * init Weblinks module
    */
    public function Install()
    {
        if (version_compare(Zikula_Core::VERSION_NUM, '1.2.0', '<')) {
            SessionUtil::setVar('errormsg', $this->__('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.'));
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
    public function Upgrade($oldversion)
    {
        // Get database information
        $dbtable = DBUtil::getTables();

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
            $dbtable = DBUtil::getTables();
            $hookscolumn = $dbtable['hooks_column'];
            $object = array('smodule' => 'Weblinks');
            $where = "WHERE $hookscolumn[smodule] = 'Web_Links'";
            DBUtil::updateObject($object, 'hooks', $where, 'id');

            // rename hooks entries
            if (ModUtil::available('EZComments')) {
                ModUtil::dbInfoLoad('EZComments');
                $dbtable = DBUtil::getTables();
                $ezccolumn = $dbtable['EZComments_column'];
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
                $dbtable = DBUtil::getTables();
                $ratcolumn = $dbtable['ratings_column'];
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
                SessionUtil::setVar('errormsg', $this->__('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.'));
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
    public function Uninstall()
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
}