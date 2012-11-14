<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
use \Weblinks_Entity_Link as Link;

class Weblinks_Controller_User extends Zikula_AbstractController
{

    /**
     * function main
     */
    public function main()
    {
        $this->redirect(ModUtil::url('Weblinks', 'user', 'view'));
    }

    /**
     * function view
     */
    public function view()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get all categories
        $categories = $this->entityManager->getRepository('Weblinks_Entity_Category')->getAll();

        // value of the function is checked
        if (!$categories) {
            return DataUtil::formatForDisplayHTML($this->__('No existing categories'));
        }

        $this->view->assign('categories', $categories)
                ->assign('numrows', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount())
                ->assign('catnum', $this->entityManager->getRepository('Weblinks_Entity_Category')->getCount())
                ->assign('helper', array('main' => 1));
        if ($this->getVar('featurebox') == 1) {
            $this->view->assign('blocklast', $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::ACTIVE, ">=", 0, 'date', 'DESC', $this->getVar('linksinblock')))
                    ->assign('blockmostpop', $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::ACTIVE, ">=", 0, 'hits', 'DESC', $this->getVar('linksinblock')));
        }

        return $this->view->fetch('user/view.tpl');
    }

    /**
     * function category
     */
    public function category()
    {
        // get parameters we need
        $cid = (int)$this->getPassedValue('cid', null, 'GET');
        $orderby = ModUtil::apiFunc('Weblinks', 'user', 'orderby', array('orderby' => $this->getPassedValue('orderby', 'titleA', 'GET')));
        $startnum = (int)$this->getPassedValue('startnum', 1, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get category vars
        $category = $this->entityManager->find('Weblinks_Entity_Category', $cid);

        // get subcategories in this category
        $subcategory = $this->entityManager->getRepository('Weblinks_Entity_Category')->getAll('title', $cid);

        // get links in this category
        $weblinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::ACTIVE, ">=", $cid, $orderby['sortby'], $orderby['sortdir'], $this->getVar('perpage'), $startnum);
        $numitems = $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount(Link::ACTIVE, ">=", $cid);

        $this->view->assign('orderby', $orderby)
                ->assign('category', $category)
                ->assign('subcategory', $subcategory)
                ->assign('weblinks', $weblinks)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 0, 
                    'details' => 0))
                ->assign('pagernumitems', $numitems);

        return $this->view->fetch('user/category.tpl');
    }

    /**
     * function visit
     */
    public function visit()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GET');

        // get link
        $linkObj = $this->entityManager->find('Weblinks_Entity_Link', $lid);

        if (!isset($linkObj)) {
            return $this->registerError($this->__('Link does not exist!'));
        }
        $link = $linkObj->toArray();

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Category', "::$link[cat_id]", ACCESS_READ)) {
            return LogUtil::registerPermissionError();
            $this->redirect(ModUtil::url('Weblinks', 'user', 'view'));
        }

        // set the counter for the link +1
        $this->entityManager->getRepository('Weblinks_Entity_Link')->addHit($linkObj);

        // is the URL local?
        if (preg_match('/^http:|^ftp:|^https:/i', $link['url'])) {
            $this->redirect($link['url']);
        } else {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $link['url']);
        }

    }

    /**
     * function search
     */
    public function search()
    {
        // get parameters we need
        $query = $this->getPassedValue('query', null, 'GETPOST');
        $orderby = ModUtil::apiFunc('Weblinks', 'user', 'orderby', array('orderby' => $this->getPassedValue('orderby', 'titleA', 'GETPOST')));
        $startnum = (int)$this->getPassedValue('startnum', 1, 'GETPOST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get categories with $query inside
        $categories = ModUtil::apiFunc('Weblinks', 'user', 'searchcats', array('query' => $query));

        // get weblinks with $query inside
        $weblinks = ModUtil::apiFunc('Weblinks', 'user', 'search_weblinks', array(
            'query' => $query,
            'orderby' => $orderby,
            'startnum' => $startnum,
            'limit' => $this->getVar('linksresults')));

        $this->view->assign('query', $query)
                ->assign('categories', $categories)
                ->assign('orderby', $orderby)
                ->assign('startnum', $startnum)
                ->assign('weblinks', $weblinks)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 1, 
                    'details' => 0))
                ->assign('pagernumlinks', ModUtil::apiFunc('Weblinks', 'user', 'countsearchlinks', array('query' => $query)));


        return $this->view->fetch('user/searchresults.tpl');
    }

    /**
     * function randomlink
     */
    public function randomlink()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get random link id
        $randomLinkId = ModUtil::apiFunc('Weblinks', 'user', 'random', array('num' => 1));
        
        if ($randomLinkId > 0) {
            $url = ModUtil::url('Weblinks', 'user', 'visit', array('lid' => $randomLinkId));
        } else {
            $url = ModUtil::url('Weblinks', 'user', 'view');
        }
        
        // redirect
        $this->redirect($url);
    }

    /**
     * function viewlinkdetails
     */
    public function viewlinkdetails()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get link details
        $weblink = $this->entityManager->find('Weblinks_Entity_Link', $lid)->toArray();

        $this->view->assign('link', $weblink)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 1, 
                    'details' => 1));

        return $this->view->fetch('user/details.tpl');
    }

    /**
     * function newlinks
     */
    public function newlinks()
    {
        // get parameters we need
        $newlinkshowdays = (int)$this->getPassedValue('newlinkshowdays', '7', 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        $this->view->assign('newlinkshowdays', $newlinkshowdays)
                ->assign('helper', array('main' => 0));

        return $this->view->fetch('user/newlinks.tpl');
    }

    /**
     * function newlinksdate
     */
    public function newlinksdate()
    {
        // get parameters we need
        $selectdate = (int)$this->getPassedValue('selectdate', null, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // count weblinks from the selected day
        $totallinks = ModUtil::apiFunc('Weblinks', 'user', 'totallinks', array('selectdate' => $selectdate));

        // get weblinks from the selected day
        $weblinks = ModUtil::apiFunc('Weblinks', 'user', 'weblinksbydate', array('selectdate' => $selectdate));

        $this->view->assign('dateview', DateUtil::getDatetime($selectdate, 'datebrief'))
                ->assign('totallinks', $totallinks)
                ->assign('weblinks', $weblinks)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 1, 
                    'details' => 0));

        return $this->view->fetch('user/newlinksdate.tpl');
    }

    /**
     * function mostpopular
     */
    public function mostpopular()
    {
        // get parameters we need
        $ratenum = (int)$this->getPassedValue('ratenum', null, 'GET');
        $ratetype = $this->getPassedValue('ratetype', null, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        $mostpoplinkspercentrigger = $this->getVar('mostpoplinkspercentrigger');
        $mostpoplinks = $this->getVar('mostpoplinks');
        $toplinkspercent = 0;
        $totalmostpoplinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount();

        if ($ratenum != "" && $ratetype != "") {
            if (!is_numeric($ratenum)) {
                $ratenum = 5;
            }
            if ($ratetype != "percent") {
                $ratetype = "num";
            }
            $mostpoplinks = $ratenum;
            if ($ratetype == "percent") {
                $mostpoplinkspercentrigger = 1;
            }
        }

        if ($mostpoplinkspercentrigger == 1) {
            $toplinkspercent = $mostpoplinks;
            $mostpoplinks = $mostpoplinks / 100;
            $mostpoplinks = $totalmostpoplinks * $mostpoplinks;
            $mostpoplinks = round($mostpoplinks);
            $mostpoplinks = max(1, $mostpoplinks);
        }

        // get most popular weblinks
        $weblinks = $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::ACTIVE, ">=", 0, 'hits', 'DESC', $mostpoplinks);

        $this->view->assign('mostpoplinkspercentrigger', $mostpoplinkspercentrigger)
                ->assign('toplinkspercent', $toplinkspercent)
                ->assign('totalmostpoplinks', $totalmostpoplinks)
                ->assign('mostpoplinks', $mostpoplinks)
                ->assign('weblinks', $weblinks)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 1, 
                    'details' => 0));

        return $this->view->fetch('user/mostpopular.tpl');
    }

    /**
     * function brokenlink
     */
    public function brokenlink()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GET');

        // Security check
        if (!$this->getVar('unregbroken') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        if (UserUtil::isLoggedIn()) {
            $submitter = UserUtil::getVar('uname');
        } else {
            $submitter = System::getVar("anonymous");
        }

        $this->view->assign('lid', $lid)
                ->assign('submitter', $submitter)
                ->assign('helper', array('main' => 0));

        return $this->view->fetch('user/brokenlink.tpl');
    }

    /**
     * function brokenlinks
     */
    public function brokenlinks()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'POST');
        $submitter = $this->getPassedValue('submitter', null, 'POST');

        // Security check
        if (!$this->getVar('unregbroken') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $this->checkCsrfToken();

        // change link status to ACTIVE_BROKEN
        $link = $this->entityManager->find('Weblinks_Entity_Link', $lid);
        if ($link) {
            $link->setStatus(Link::ACTIVE_BROKEN);
            $link->setModifysubmitter($submitter);
            $this->entityManager->flush();
        } else {
            LogUtil::registerError($this->__('No link found with that id.'));
            $this->redirect(ModUtil::url('Weblinks', 'user', 'view'));
        }

        $this->view->assign('helper', array('main' => 0));

        return $this->view->fetch('user/brokenlinks.tpl');
    }

    /**
     * function modifylinkrequest
     */
    public function modifylinkrequest()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GET');

        // Security check
        if (!$this->getVar('blockunregmodify') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        // get link vars
        $link = $this->entityManager->find('Weblinks_Entity_Link', $lid)->toArray();

        if (UserUtil::isLoggedIn()) {
            $submitter = UserUtil::getVar('uname');
        } else {
            $submitter = System::getVar("anonymous");
        }

        $this->view->assign('blockunregmodify', $this->getVar('blockunregmodify'))
                ->assign('link', $link)
                ->assign('submitter', $submitter)
                ->assign('anonymous', System::getVar("anonymous"))
                ->assign('helper', array('main' => 0));

        return $this->view->fetch('user/modifylinkrequest.tpl');
    }

    /**
     * function modifylinkrequests
     */
    public function modifylinkrequests()
    {
        // get parameters we need
        $modlink = $this->getPassedValue('modlink', array(), 'POST');

        // Security check
        if (!$this->getVar('blockunregmodify') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $this->checkCsrfToken();

        // add link request
        ModUtil::apiFunc('Weblinks', 'user', 'modifylinkrequest', $modlink);

        $this->view->assign('helper', array('main' => 0));

        return $this->view->fetch('user/modifylinkrequests.tpl');
    }

    /**
     * function addlink
     */
    public function addlink()
    {
        // Security check
        if (!$this->getVar('links_anonaddlinklock') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            $addlink = false;
        } else {
            $addlink = true;
        }

        $this->view->assign('addlink', $addlink)
                ->assign('helper', array('main' => 0));
        if (UserUtil::isLoggedIn()) {
            $this->view->assign('submitter', UserUtil::getVar('uname'))
                    ->assign('submitteremail', UserUtil::getVar('email'));
        }

        return $this->view->fetch('user/addlink.tpl');
    }

    /**
     * function add
     */
    public function add()
    {
        // get parameters we need
        $newlink = $this->getPassedValue('newlink', array(), 'POST');

        // Security check
        if (!$this->getVar('links_anonaddlinklock') == 1 &&
                !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $this->checkCsrfToken();

        // write the link to db and get a status message back
        $result = ModUtil::apiFunc('Weblinks', 'user', 'add', $newlink);

        $this->view->assign('submit', $result['submit'])
                ->assign('text', $result['text'])
                ->assign('helper', array('main' => 0));

        return $this->view->fetch('user/add.tpl');
    }

    /**
     * helper function to convert old getPassedValue method to Core 1.3.3-standard
     * 
     * @param string $variable
     * @param mixed $defaultValue
     * @param string $type
     * @return mixed 
     */
    private function getPassedValue($variable, $defaultValue, $type = 'POST')
    {
        if ($type == 'POST') {
            return $this->request->request->get($variable, $defaultValue);
        } else if ($type == 'GET') {
            return $this->request->query->get($variable, $defaultValue);
        } else {
            // else try GET then POST
            return $this->request->query->get($variable, $this->request->request->get($variable, $defaultValue));
        }
    }

}