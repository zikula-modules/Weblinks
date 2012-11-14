<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Api_User extends Zikula_AbstractApi
{

    /**
     * order function
     */
    public function orderby($args)
    {
        switch ($args['orderby']) {
            case 'titleD':
                return array('sortby' => 'title', 'sortdir' => 'DESC');
                break;
            case 'dateA':
                return array('sortby' => 'date', 'sortdir' => 'ASC');
                break;
            case 'dateD':
                return array('sortby' => 'date', 'sortdir' => 'DESC');
                break;
            case 'hitsA':
                return array('sortby' => 'hits', 'sortdir' => 'ASC');
                break;
            case 'hitsD':
                return array('sortby' => 'hits', 'sortdir' => 'DESC');
                break;
            case 'titleA':
            default:
                return array('sortby' => 'title', 'sortdir' => 'ASC');
                break;
        }
    }

    /**
     * get categories with query inside
     */
    public function searchcats($args)
    {
        // Argument check
        if (!isset($args['query'])) {
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
//            'level' => ACCESS_READ);

        $dql = "SELECT a FROM Weblinks_Entity_Category a";
        $dql .= " WHERE a.title LIKE '%" . DataUtil::formatForStore($args['query']) . "%'";
         // generate query
        $query = $this->entityManager->createQuery($dql);

        try {
            $searchcats = $query->getResult();
        } catch (Exception $e) {
            return LogUtil::registerError($this->__('Error! Could not load items: ' . $e->getMessage()));
        }
        
        // should process for permissions here?

        // Return the subcategories array
        return $searchcats;
    }

    /**
     * get weblinks with query inside
     */
    public function search_weblinks($args)
    {
        // Argument check
        if (!isset($args['query'])) {
            return LogUtil::registerArgsError();
        }

        $query = DataUtil::formatForStore($args['query']);
        $orderBy = (isset($args['orderby'])) ? $args['orderby'] : array('sortby' => 'date', 'sortdir' => 'ASC');
        $startNum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
        $limit = (isset($args['limit']) && is_numeric($args['limit'])) ? $args['limit'] : 0;

        // define the permission filter to apply
//        $permFilter = array();
//        $permFilter[] = array('realm' => 0,
//            'component_left' => 'Weblinks',
//            'component_middle' => '',
//            'component_right' => 'Category',
//            'instance_left' => 'title',
//            'instance_middle' => '',
//            'instance_right' => 'cat_id',
//            'level' => ACCESS_READ);

        $result = $this->entityManager->getRepository('Weblinks_Entity_Link')->searchLinks($query, $orderBy['sortby'], $orderBy['sortdir'], $limit, $startNum);

        // check for db error
        if ($result === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        
        // should process result for permissions here

        // Return the array
        return $result;
    }

    /**
     * count searchlinks
     */
    public function countsearchlinks($args)
    {
        // Argument check
        if (!isset($args['query'])) {
            return LogUtil::registerArgsError();
        }

        $query = DataUtil::formatForStore($args['query']);
        $result = $this->entityManager->getRepository('Weblinks_Entity_Link')->searchLinks($query);
        
        // should process for permissions here too?

        return count($result);
    }

    /**
     * get random links
     */
    public function random($args)
    {
        $num = (isset($args['num']) && is_numeric($args['num'])) ? $args['num'] : 1;

        // define the permission filter to apply
//        $permFilter = array();
//        $permFilter[] = array('realm' => 0,
//            'component_left' => 'Weblinks',
//            'component_middle' => '',
//            'component_right' => 'Category',
//            'instance_left' => 'title',
//            'instance_middle' => '',
//            'instance_right' => 'cat_id',
//            'level' => ACCESS_READ);

        $weblinks = array();

        // this is unfortunate since every record must be retrieved in order to randomize them properly.
        $templinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks();
        if (count($templinks) > $num) {
            $randomIds = array_rand($templinks, $num);
            foreach ($randomIds as $id) {
                $weblinks[] = $templinks[$id];
            }
        } else {
            $weblinks = $templinks;
        }
        
        // extract the lids
        $lidarray = array();
        $linkTitles = array();
        foreach ($weblinks as $link) {
            $lidarray[] = $link['lid'];
            $linkTitles[$link['lid']] = $link['title'];
        }
        

        if (count($lidarray) < 1) { // if no link
            return LogUtil::registerError($this->__('Sorry! There are no links to select randomly.'));
        }


        if ($num > 1) {
            $returnValue = array();
            foreach ($lidarray as $lid) {
                $returnValue[] = array('lid' => $lid, 'title' => $linkTitles[$lid]);
            }
        } else {
            $returnValue = $lidarray[0];
        }
        
        // here there should be a check on the permissions...

        return $returnValue;
    }

    /**
     * add link rewuest to db
     */
    public function modifylinkrequest($args)
    {
        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!$this->getVar('blockunregmodify') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }
        
        // get original link
        $originalLink = $this->entityManager->find('Weblinks_Entity_Link', $args['lid']);
        // set modified values in original link
        $originalLink->setModifiedContent($args);
        $originalLink->setModifySubmitter($args['modifysubmitter']);
        // update original link status
        $originalLink->setStatus(Weblinks_Entity_Link::ACTIVE_MODIFIED);
        
        $this->entityManager->flush();
        
        return true;
    }

    /**
     * add link to db
     */
    public function add($link)
    {
        // Security check
        if (!$this->getVar('links_anonaddlinklock') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $checkurl = $this->entityManager->getRepository('Weblinks_Entity_Link')->findBy(array('url' => $link['url']));
        $valid = System::varValidate($link['url'], 'url');

        if (count($checkurl) > 0) {
            $link['text'] = $this->__('Sorry! This URL is already listed in the database!');
            $link['submit'] = 0;
            return $link;
        } else if ($valid == false) {
            $link['text'] = $this->__('Sorry! Error! You must type a URL for the web link!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($link['title'])) {
            $link['text'] = $this->__('Sorry! Error! You must type a title for the URL!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($link['cat_id']) || !is_numeric($link['cat_id'])) {
            $link['text'] = $this->__('Sorry! Error! No category!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($link['description'])) {
            $link['text'] = $this->__('Sorry! Error! You must type a description for the URL!');
            $link['submit'] = 0;
            return $link;
        // validate hooks here!
        } else {
            if (empty($link['name'])) {
                $link['name'] = System::getVar("anonymous");
            }

            $link['submitter'] = $link['name'];
            $link['status'] = Weblinks_Entity_Link::INACTIVE;

            $linkEntity = new Weblinks_Entity_Link();
        
            try {
                $linkEntity->merge($link);
                $linkEntity->setCategory($this->entityManager->find('Weblinks_Entity_Category', $link['cat_id']));
                $this->entityManager->persist($linkEntity);
                $this->entityManager->flush();
            } catch (Zikula_Exception $e) {
                return LogUtil::registerError($this->__("ERROR: The link was not created: " . $e->getMessage()));
            }

            // notify hooks here!
            $result = array();
            $result['lid'] = $linkEntity->getLid();

            if (empty($link['email'])) {
                $result['text'] = $this->__("You didn't enter an e-mail address. However, your link will still be checked.");
            } else {
                $result['text'] = $this->__("Thank you! You'll receive an e-mail message when it's approved.");
            }
            $result['submit'] = 1;
            return $result;
        }
    }

    /**
     * get the last weblinks
     */
    public function lastweblinks($args)
    {
        // erase  this method once the code is changed in the block
    }

    /**
     * get the most popular weblinks
     */
    public function mostpopularweblinks($args)
    {
        // erase  this method once the code is changed in the block
    }

}