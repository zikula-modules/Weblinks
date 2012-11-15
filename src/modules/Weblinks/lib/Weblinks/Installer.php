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

                // NOTE: old hooks rename is removed from this upgrade (here)
                // because old hooks system no longer used in v3.0.0

            case '2.0.1':
                $this->setVar('doubleurl', 0);
                $this->setVar('unregbroken', 0);
                $this->setVar('thumber', 0);
                $this->setVar('thumbersize', 'XL');

                // remove obsolete module vars
                $this->delVar('toplinks');
                $this->delVar('toplinkspercentrigger');

            case '2.1.0':
                // version 2.1.0 was not released
                $connection = $this->entityManager->getConnection();
                $prefix = $this->serviceManager['prefix'];
                $prefix = (empty($prefix)) ? '' : "_" . $prefix;
                $sqls = array();
                // correct the links_link table
                $sqls[] = 'RENAME TABLE ' . $prefix . 'links_link' . " TO links_link";
                $sqls[] ="ALTER TABLE `links_links` CHANGE `pn_lid` `lid` INT(11) NOT NULL AUTO_INCREMENT, 
CHANGE `pn_cat_id` `cat_id` INT(11) NOT NULL DEFAULT '0', 
CHANGE `pn_title` `title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `pn_url` `url` VARCHAR(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `pn_description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `pn_date` `date` DATETIME NULL DEFAULT NULL, 
CHANGE `pn_name` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `pn_email` `email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `pn_hits` `hits` INT(11) NOT NULL DEFAULT '0', 
CHANGE `pn_submitter` `submitter` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', 
CHANGE `pn_ratingsummary` `ratingsummary` DOUBLE(6,4) NOT NULL DEFAULT '0.0000', 
CHANGE `pn_totalvotes` `totalvotes` INT(11) NOT NULL DEFAULT '0',
CHANGE `pn_totalcomments` `totalcomments` INT(11) NOT NULL DEFAULT '0'";
                // correct the links_categories table
                $sqls[] = 'RENAME TABLE ' . $prefix . 'links_categories' . " TO links_categories";
                $sqls[] = "ALTER TABLE  `links_categories` CHANGE  `pn_cat_id`  `cat_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE  `pn_parent_id`  `parent_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE  `pn_title`  `title` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '',
CHANGE  `pn_description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
                foreach ($sqls as $sql) {
                    $stmt = $connection->prepare($sql);
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                        LogUtil::registerError($e->getMessage());
                    }
                }
                // update to current entities (adds some columns) & set status to active
                DoctrineHelper::updateSchema($this->entityManager, array('Weblinks_Entity_Link', 'Weblinks_Entity_Category'));
                $sql = "UPDATE links_links SET status=" . Weblinks_Entity_Link::ACTIVE;
                $stmt = $connection->prepare($sql);
                $stmt->execute();

                // move links from 'links_newlink' to 'links_link' with status INACTIVE
                $sql = "SELECT * FROM {$prefix}links_newlink";
                $objects = $connection->fetchAll($sql);
                foreach ($objects as $object) {
                    $link = new Weblinks_Entity_Link();
                    $link->setTitle($object['pn_title']);
                    $link->setDescription($object['pn_description']);
                    $link->setUrl($object['pn_url']);
                    $link->setName($object['pn_name']);
                    $link->setEmail($object['pn_email']);
                    $link->setSubmitter($object['pn_submitter']);
                    $link->setStatus(Weblinks_Entity_Link::INACTIVE);
                    $link->setCategory($this->entityManager->find('Weblinks_Entity_Category', $object['pn_cat_id']));
                    $this->entityManager->persist($link);
                }
                $this->entityManager->flush();
                // remove 'links_newlink' table
                $sql = "DROP TABLE `{$prefix}links_newlink`";
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                
                // move links from 'links_modrequest' to 'links_links' into the '$modifiedContent' column as serialized array
                // with status ACTIVE_MODIFIED
                $sql = "SELECT * FROM {$prefix}links_modrequest";
                $objects = $connection->fetchAll($sql);
                foreach ($objects as $object) {
                    $link = $this->entityManager->find('Weblinks_Entity_Link', $object['lid']);
                    $link->setModifysubmitter($object['modifysubmitter']);
                    if ($object['pn_brokenlink'] == 1) {
                        $link->setStatus(Weblinks_Entity_Link::ACTIVE_BROKEN);
                    } else {
                        $link->setModifiedContent(array(
                            'title' => 'pn_title',
                            'url' => 'pn_url',
                            'description' => 'pn_description',
                            'cat_id' => 'pn_cat_id',
                        ));
                        $link->setStatus(Weblinks_Entity_Link::ACTIVE_MODIFIED);
                    }
                    $this->entityManager->persist($link);
                }
                $this->entityManager->flush();
                // remove 'links_modrequest' table
                $sql = "DROP TABLE `{$prefix}links_modrequest`";
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                // remove 'links_votedata' table
                $sql = "DROP TABLE IF EXISTS `{$prefix}links_votedata`";
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                // remove 'links_editorials' table
                $sql = "DROP TABLE IF EXISTS `{$prefix}links_editorials`";
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                
                // register new hooks
                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
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
        //drop the tables
        DoctrineHelper::dropSchema($this->entityManager, array(
            'Weblinks_Entity_Link',
            'Weblinks_Entity_Category'));
        $this->delVars();

        // remove hooks
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());

        // Deletion successful
        return true;
    }

}