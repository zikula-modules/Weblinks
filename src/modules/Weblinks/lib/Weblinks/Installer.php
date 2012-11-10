<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Installer extends Zikula_AbstractInstaller
{

    /**
     * init Weblinks module
     */
    public function Install()
    {
        // create tables
        try {
            DoctrineHelper::createSchema($this->entityManager, array(
                'Weblinks_Entity_Link',
                'Weblinks_Entity_Category'));
        } catch (Exception $e) {
            LogUtil::registerError($this->__f('Error! Could not create tables (%s).', $e->getMessage()));
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

        // register hooks
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

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

        switch ($oldversion) {
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
                    for ($cnt = 0; $cnt < $ezccount; $cnt++) {
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
                    for ($cnt = 0; $cnt < $ratcount; $cnt++) {
                        $ratarray[$cnt]['module'] = str_replace('Web_Links', 'Weblinks', $ratarray[$cnt]['module']);
                    }
                    DBUtil::updateObjectArray($ratarray, 'ratings', 'rid');
                }

            case '2.0.1':

                if (version_compare(Zikula_Core::VERSION_NUM, '1.2.0', '<')) {
                    SessionUtil::setVar('errormsg', $this->__('Error! This version of the Weblinks module requires Zikula 1.2.0 or later. Installation has been stopped because this requirement is not met.'));
                    return false;
                }

                $this->setVar('doubleurl', 0);
                $this->setVar('unregbroken', 0);
                $this->setVar('thumber', 0);
                $this->setVar('thumbersize', 'XL');

                // remove obsolete module vars
                $this->delVar('toplinks');
                $this->delVar('toplinkspercentrigger');

            case '2.1.0':
                // not released
                // register new hooks
                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
                // need to move links from 'links_newlink' to 'links_link' with status INACTIVE
                // then remove 'links_newlink' table
                // need to move links from 'links_modrequest' to 'links_links' into the '$modifiedContent' column as serialized object
                // then remove 'links_modrequest' table
                // remove 'links_votedata' table
                // remove 'links_editorials' table
            
            case '3.0.0':
                // future code
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

        // remove hooks
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());

        // Deletion successful
        return true;
    }

}