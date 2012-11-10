<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
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
        $categories = ModUtil::apiFunc('Weblinks', 'user', 'categories');

        // value of the function is checked
        if (!$categories) {
            return DataUtil::formatForDisplayHTML($this->__('No existing categories'));
        }

        $this->view->assign('categories', $categories)
                ->assign('numrows', ModUtil::apiFunc('Weblinks', 'user', 'numrows'))
                ->assign('catnum', ModUtil::apiFunc('Weblinks', 'user', 'catnum'))
                ->assign('helper', array('main' => 1));
        if ($this->getVar('featurebox') == 1) {
            $this->view->assign('blocklast', ModUtil::apiFunc('Weblinks', 'user', 'lastweblinks'))
                    ->assign('blockmostpop', ModUtil::apiFunc('Weblinks', 'user', 'mostpopularweblinks'));
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
        $orderby = $this->getPassedValue('orderby', 'titleA', 'GET');
        $startnum = (int)$this->getPassedValue('startnum', 1, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get category vars
        $category = ModUtil::apiFunc('Weblinks', 'user', 'category', array('cid' => $cid));

        // get subcategories in this category
        $subcategory = ModUtil::apiFunc('Weblinks', 'user', 'subcategory', array('cid' => $cid));

        // get links in this category
        $weblinks = ModUtil::apiFunc('Weblinks', 'user', 'weblinks', array(
            'cid' => $cid,
            'orderbysql' => ModUtil::apiFunc('Weblinks', 'user', 'orderby', array('orderby' => $orderby)),
            'startnum' => $startnum,
            'numlinks' => $this->getVar('perpage')));

        $this->view->assign('orderby', $orderby)
                ->assign('category', $category)
                ->assign('subcategory', $subcategory)
                ->assign('weblinks', $weblinks)
                ->assign('helper', array(
                    'main' => 0, 
                    'showcat' => 0, 
                    'details' => 0));
        $this->view->assign('pagernumitems', ModUtil::apiFunc('Weblinks', 'user', 'countcatlinks', array('cid' => $cid)));

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
        $link = ModUtil::apiFunc('Weblinks', 'user', 'link', array('lid' => $lid));

        // the return value of the function is checked here
        if ($link == false) {
            return $this->registerError($this->__('Link don\'t exist!'));
        }

        // Security check
        if (!SecurityUtil::checkPermission('Weblinks::Category', "::$link[cat_id]", ACCESS_READ)) {
            return LogUtil::registerPermissionError();
            $this->redirect(ModUtil::url('Weblinks', 'user', 'view'));
        }

        // set the counter for the link +1
        ModUtil::apiFunc('Weblinks', 'user', 'hitcountinc', array('lid' => $lid, 'hits' => $link['hits']));

        // is the URL local?
        if (eregi('^http:|^ftp:|^https:', $link['url'])) {
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
        $orderby = $this->getPassedValue('orderby', 'titleA', 'GETPOST');
        $startnum = (int)$this->getPassedValue('startnum', 1, 'GETPOST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // get categories with $query inside
        $categories = ModUtil::apiFunc('Weblinks', 'user', 'searchcats', array('query' => $query));

        // get weblinks with $query inside
        $weblinks = ModUtil::apiFunc('Weblinks', 'user', 'search_weblinks', array(
            'query' => $query,
            'orderbysql' => ModUtil::apiFunc('Weblinks', 'user', 'orderby', array('orderby' => $orderby)),
            'startnum' => $startnum,
            'numlinks' => $this->getVar('linksresults')));

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

        // get random link id and redirect to the visit function
        $this->redirect(ModUtil::url('Weblinks', 'user', 'visit', array('lid' => ModUtil::apiFunc('Weblinks', 'user', 'random'))));
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
        $weblink = ModUtil::apiFunc('Weblinks', 'user', 'link', array('lid' => $lid));

        $this->view->assign('weblinks', $weblink)
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
        $totalmostpoplinks = ModUtil::apiFunc('Weblinks', 'user', 'numrows');

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
        $weblinks = ModUtil::apiFunc('Weblinks', 'user', 'mostpopularweblinks', array('mostpoplinks' => $mostpoplinks));

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
     * function brockenlink
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
     * function brockenlinks
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

        // add broken link
        ModUtil::apiFunc('Weblinks', 'user', 'addbrokenlink', array(
            'lid' => $lid, 
            'submitter' => $submitter));

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
        $link = ModUtil::apiFunc('Weblinks', 'user', 'link', array('lid' => $lid));

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
        ModUtil::apiFunc('Weblinks', 'user', 'modifylinkrequest', array(
            'lid' => $modlink['lid'],
            'cid' => $modlink['cid'],
            'title' => $modlink['title'],
            'url' => $modlink['url'],
            'description' => $modlink['description'],
            'submitter' => $modlink['submitter']));

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
        $link = ModUtil::apiFunc('Weblinks', 'user', 'add', array(
            'title' => $newlink['title'],
            'url' => $newlink['url'],
            'cat' => $newlink['cat'],
            'description' => $newlink['description'],
            'submitter' => $newlink['submitter'],
            'submitteremail' => $newlink['submitteremail']));

        $this->view->assign('submit', $link['submit'])
                ->assign('text', $link['text'])
                ->assign('helper', array('main' => 0));

        return $this->view->fetch('user/add.tpl');
    }

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