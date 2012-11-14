<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
use \Weblinks_Entity_Link as Link;

class Weblinks_Api_Admin extends Zikula_AbstractApi
{

    /**
     * get available admin panel links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT)) {
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'admin', 'view'),
                'text' => $this->__('Overview'),
                'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('Weblinks::Category', '::', ACCESS_EDIT)) {
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'admin', 'catview'),
                'text' => $this->__('Categories administration'),
                'class' => 'z-icon-es-cubes');
        }
        if (SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_EDIT)) {
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'admin', 'linkview'),
                'text' => $this->__('Links administration'),
                'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'admin', 'getconfig'),
                'text' => $this->__('Settings'),
                'class' => 'z-icon-es-config');
        }
        if (SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_EDIT)) {
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'admin', 'help'),
                'text' => $this->__('Help'),
                'class' => 'z-icon-es-help');
            $links[] = array(
                'url' => ModUtil::url('Weblinks', 'user', 'view'),
                'text' => $this->__('User Link-Index'),
                'class' => 'z-icon-es-url');
        }
        return $links;
    }

    /**
     * count brocken links
     */
    public function countbrokenlinks()
    {
        return DBUtil::selectObjectCountByID('links_modrequest', 1, 'brokenlink');
    }

    /**
     * count modrequests for links
     */
    public function countmodrequests()
    {
        $dbtable = DBUtil::getTables();
        $column = $dbtable['links_modrequest_column'];
        $where = "WHERE $column[brokenlink] = '0'";
        return DBUtil::selectObjectCount('links_modrequest', $where);
    }

    /**
     * get new links waiting for approve
     */
    public function newlinks()
    {
        // define the permission filter to apply
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
            'component_left' => 'Weblinks',
            'component_middle' => '',
            'component_right' => 'Category',
            'instance_left' => 'title',
            'instance_middle' => '',
            'instance_right' => 'cat_id',
            'level' => ACCESS_DELETE);

        // get newlinks vars from db
        $newlinks = DBUtil::selectObjectArray('links_newlink', '', 'lid', '-1', '-1', '', $permFilter);

        // check for a db error
        if ($newlinks === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the newlinks array
        return $newlinks;
    }

    /**
     * add/modify a category
     */
    public function editcategory($category)
    {
        // Argument check
        if (!isset($category['parent_id']) || !is_numeric($category['parent_id']) || !isset($category['title'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        if ($this->entityManager->getRepository('Weblinks_Entity_Category')->exists($category)) {
            return LogUtil::registerError($this->__('ERROR: The category title exists on this level.'));
        }

        if (isset($category['cat_id'])) {
            $catEntity = $this->entityManager->find('Weblinks_Entity_Category', $category['cat_id']);
        } else {
            $catEntity = new Weblinks_Entity_Category();
        }
        
        try {
            $catEntity->merge($category);
            $this->entityManager->persist($catEntity);
            $this->entityManager->flush();
        } catch (Zikula_Exception $e) {
            return LogUtil::registerError($this->__("ERROR: The category was not created: " . $e->getMessage()));
        }

        return true;
    }

    /**
     * delete a category
     */
    public function delcategory($args)
    {
        // Argument check
        if (!isset($args['cid']) || !is_numeric($args['cid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }
        
        $cat = $this->entityManager->find('Weblinks_Entity_Category', $args['cid']);
        $this->recursiveCategoryRemoval($cat);
        
        return true;
    }
    
    /**
     * Private function to handle recursive category removal
     * @param Weblinks_Entity_Category $cat 
     */
    private function recursiveCategoryRemoval(Weblinks_Entity_Category $cat)
    {
        $cid = $cat->getCat_id();
        // is category a parent?
        $children = $this->entityManager->getRepository('Weblinks_Entity_Category')->findBy(array('parent_id' => $cid));
        if (isset($children)) {
            foreach ($children as $child) {
                $this->recursiveCategoryRemoval($child);
            }
        }
        // remove the links in the category
        $links = $this->entityManager->getRepository('Weblinks_Entity_Link')->findBy(array('category' => $cid));
        if (isset($links)) {
            foreach ($links as $link) {
                $this->entityManager->remove($link);
            }
        }
        // remove the category
        $this->entityManager->remove($cat);
        $this->entityManager->flush();
    }

    /**
     * edit/create a link
     */
    public function editlink($link)
    {
        // Argument check
        if (!isset($link['cat_id']) || !isset($link['title']) || !isset($link['url'])) {
            return LogUtil::registerArgsError();
        }
        unset($link['new']);

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        if (isset($link['lid'])) {
            $linkEntity = $this->entityManager->find('Weblinks_Entity_Link', $link['lid']);
        } else {
            $linkEntity = new Weblinks_Entity_Link();
        }
//        $status = (isset($link['new']) && ($link['new'] == 1)) ? Link::INACTIVE : Link::ACTIVE;
        $status = Link::ACTIVE;
        
        try {
            $linkEntity->merge($link);
            $linkEntity->setStatus($status);
            $linkEntity->setCategory($this->entityManager->find('Weblinks_Entity_Category', $link['cat_id']));
            $this->entityManager->persist($linkEntity);
            $this->entityManager->flush();
        } catch (Zikula_Exception $e) {
            return LogUtil::registerError($this->__("ERROR: The link was not created: " . $e->getMessage()));
        }

        return $linkEntity->getLid();
    }

    /**
     * delete a newlink
     */
    public function delnewlink($args)
    {
        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }

        // delete link from the newlink table
        if (!DBUtil::deleteObjectByID('links_newlink', $args['lid'], 'lid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
    }

    /**
     * get link vars for modify
     */
    public function getlink($args) //ready
    {
        // Argument check
        if ((!isset($args['lid']) || !is_numeric($args['lid']))) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // define the permission filter to apply
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
            'component_left' => 'Weblinks',
            'component_middle' => '',
            'component_right' => 'Link',
            'instance_left' => 'title',
            'instance_middle' => '',
            'instance_right' => 'lid',
            'level' => ACCESS_EDIT);

        // get the object from the db
        $link = DBUtil::selectObjectById('links_links', $args['lid'], 'lid', '', $permFilter);

        // check for a db error
        if ($link === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return $link;
    }

    /**
     * update link vars
     */
    public function updatelink($args)
    {
        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid']) ||
                !isset($args['cid']) || !is_numeric($args['cid']) ||
                !isset($args['title']) || !isset($args['url'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $dbtable = DBUtil::getTables();
        $column = $dbtable['links_links_column'];
        $items = array('cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description'], 'name' => $args['name'], 'email' => $args['email'], 'hits' => $args['hits']);
        $where = "WHERE $column[lid]='" . (int)DataUtil::formatForStore($args['lid']) . "'";
        if (!DBUtil::updateObject($items, 'links_links', $where, 'lid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
    }

    /**
     * delete a link
     */
//    public function dellink($args)
//    {
//        // Argument check
//        if (!isset($args['lid']) || !is_numeric($args['lid'])) {
//            return LogUtil::registerArgsError();
//        }
//
//        // Security check
//        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
//            return LogUtil::registerPermissionError();
//        }
//
//        if (!DBUtil::deleteObjectByID('links_links', $args['lid'], 'lid')) {
//            return LogUtil::registerError($this->__('Error! Could not delete item.'));
//        }
//
//        return true;
//    }

    /**
     * check links
     */
    public function checklinks($args)
    {
        // Argument check
        if (!isset($args['cid']) || !is_numeric($args['cid'])) {
            return LogUtil::registerArgsError();
        }

        // define the permission filter to apply
//        $permFilter = array();
//        $permFilter[] = array('realm' => 0,
//            'component_left' => 'Weblinks',
//            'component_middle' => '',
//            'component_right' => 'Category',
//            'instance_left' => 'title',
//            'instance_middle' => '',
//            'instance_right' => 'cat_id',
//            'level' => ACCESS_EDIT);

        if ((int)$args['cid'] == 0) {
            $checkcatlinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks();
        } else {
            $checkcatlinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::ACTIVE, ">=", $args['cid']);
        }


        // put items into result array.
        $links = array();
        foreach ($checkcatlinks as $link) {
            
            // check perms here
            
            if ($link['url'] == 'http://' || $link['url'] == '') {
                $fp = false;
            } else {
                $vurl = parse_url($link['url']);
                $fp = fsockopen($vurl['host'], 80, $errno, $errstr, 15);
            }

            $links[] = array('lid' => $link['lid'],
                'title' => $link['title'],
                'url' => $link['url'],
                'fp' => $fp);
        }

        // return array
        return $links;
    }

    /**
     * get broken links
     */
    public function brokenlinks()
    {
        $dbtable = DBUtil::getTables();
        $column = $dbtable['links_modrequest_column'];
        $where = "WHERE $column[brokenlink] = '1'";

        // define the permission filter to apply
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
            'component_left' => 'Weblinks',
            'component_middle' => '',
            'component_right' => 'Category',
            'instance_left' => 'title',
            'instance_middle' => '',
            'instance_right' => 'cat_id',
            'level' => ACCESS_EDIT);

        // get the vars from the db
        $objArray = DBUtil::selectObjectArray('links_modrequest', $where, 'requestid', '-1', '-1', '', $permFilter);

        // check for a db error
        if ($objArray === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // put items into result array.
        $brokenlinks = array();
        foreach ($objArray as $request) {
            $link = ModUtil::apiFunc('Weblinks', 'admin', 'getlink', array('lid' => $request['lid']));

            if ($request['modifysubmitter'] != System::getVar('anonymous')) {
                $email = DBUtil::selectObjectByID('users', $request['modifysubmitter'], 'uname');
            }

            $brokenlinks[] = array('lid' => $request['lid'],
                'rid' => $request['requestid'],
                'title' => $link['title'],
                'url' => $link['url'],
                'submitter' => $request['modifysubmitter'],
                'submitteremail' => $email['email'],
                'owner' => $link['name'],
                'owneremail' => $link['email']);
        }

        // return array
        return $brokenlinks;
    }

    /**
     * delete a request
     */
    public function delrequest($args)
    {
        // Argument check
        if (!isset($args['rid']) || !is_numeric($args['rid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }

        if (!DBUtil::deleteObjectByID('links_modrequest', $args['rid'], 'requestid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
    }

    /**
     * get links with modrequest
     */
    public function modrequests()
    {

        // define the permission filter to apply
//        $permFilter = array();
//        $permFilter[] = array('realm' => 0,
//            'component_left' => 'Weblinks',
//            'component_middle' => '',
//            'component_right' => 'Category',
//            'instance_left' => 'title',
//            'instance_middle' => '',
//            'instance_right' => 'cat_id',
//            'level' => ACCESS_EDIT);

        // get the links from the db
        $modifiedLinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->findBy(array('status' => Link::ACTIVE_MODIFIED));

        // put items into result array.
        $modrequests = array();
        foreach ($modifiedLinks as $link) {
            
            // should process for permissions here

            if ($link->getModifysubmitter() != System::getVar('anonymous')) {
                // ewww. forced to use DBUtil...
                $email = DBUtil::selectObjectByID('users', $link->getModifysubmitter(), 'uname');
            }
            $modifiedContent = $link->getModifiedContent();
            $modifiedCategory = $this->entityManager->find('Weblinks_Entity_Category', $modifiedContent['cat_id']);

            $modrequests[] = array(
                'lid' => $link->getLid(),
                'title' => $modifiedContent['title'],
                'url' => $modifiedContent['url'],
                'description' => $modifiedContent['description'],
                'cid' => $modifiedCategory->getCat_id(),
                'cidtitle' => $modifiedCategory->getTitle(),
                'origtitle' => $link->getTitle(),
                'origurl' => $link->getUrl(),
                'origdescription' => $link->getDescription(),
                'origcid' => $link->getCat_id(),
                'origcidtitle' => $link->getCategory()->getTitle(),
                'submitter' => $link->getModifysubmitter(),
                'submitteremail' => $email['email'],
                'owner' => $link->getName(),
                'owneremail' => $link->getEmail());
        }

        // return array
        return $modrequests;
    }

    /**
     * get link with modrequest
     */
    public function linkmodrequest($args)
    {
        // Argument check
        if ((!isset($args['rid']) || !is_numeric($args['rid']))) {
            return LogUtil::registerArgsError();
        }

        // define the permission filter to apply
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
            'component_left' => 'Weblinks',
            'component_middle' => '',
            'component_right' => 'Link',
            'instance_left' => 'title',
            'instance_middle' => '',
            'instance_right' => 'lid',
            'level' => ACCESS_EDIT);

        // get the object from the db
        $requestlink = DBUtil::selectObjectById('links_modrequest', $args['rid'], 'requestid', '', $permFilter);

        // check for a db error
        if ($requestlink === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return $requestlink;
    }

    /**
     * update link vars from modrequest
     */
    public function updatemodlink($args)
    {
        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid']) ||
                !isset($args['cid']) || !is_numeric($args['cid']) ||
                !isset($args['title']) || !isset($args['url'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $dbtable = DBUtil::getTables();
        $column = $dbtable['links_links_column'];
        $items = array('cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description']);
        $where = "WHERE $column[lid]='" . (int)DataUtil::formatForStore($args['lid']) . "'";
        if (!DBUtil::updateObject($items, 'links_links', $where, 'lid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
    }

}