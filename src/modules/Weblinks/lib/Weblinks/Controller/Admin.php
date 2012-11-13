<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
use \Weblinks_Entity_Link as Link;

class Weblinks_Controller_Admin extends Zikula_AbstractController
{

    /**
     * function main
     */
    public function main()
    {
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'view'));
    }

    /**
     * function view
     */
    public function view()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->view->assign('numrows', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount())
                ->assign('catnum', $this->entityManager->getRepository('Weblinks_Entity_Category')->getCount())
                ->assign('totalbrokenlinks', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount(Link::ACTIVE_BROKEN, '='))
                ->assign('totalmodrequests', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount(Link::ACTIVE_MODIFIED, '='))
                ->assign('newlinks', $this->entityManager->getRepository('Weblinks_Entity_Link')->getLinks(Link::INACTIVE));

        return $this->view->fetch('admin/view.tpl');
    }

    /**
     * function catview
     */
    public function catview()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->view->assign('catnum', $this->entityManager->getRepository('Weblinks_Entity_Category')->getCount());

        return $this->view->fetch('admin/catview.tpl');
    }

    /**
     * function addcategory
     */
    public function addcategory()
    {
        // get parameters we need
        $newCategory = $this->getPassedValue('newcategory', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADD), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // check and add a new category
        if (ModUtil::apiFunc('Weblinks', 'admin', 'editcategory', $newCategory)) {
            // Success
            $this->registerStatus($this->__('Category successfully added'));
        }

        // redirect to function catview
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'catview'));
    }

    /**
     * function modcategory
     */
    public function modcategory()
    {
        // get parameters we need
        $cid = (int)$this->getPassedValue('cid', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        $this->view->assign('category', $this->entityManager->find('Weblinks_Entity_Category', $cid)->toArray());

        return $this->view->fetch('admin/modcategory.tpl');
    }

    /**
     * function savemodcategory
     */
    public function savemodcategory()
    {
        // get parameters we need
        $modifiedCategory = $this->getPassedValue('modifiedcategory', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // update the category with new vars
        if (ModUtil::apiFunc('Weblinks', 'admin', 'editcategory', $modifiedCategory)) {
            // Success
            $this->registerStatus($this->__('Category successfully modified'));
        }

        // redirect to function catview
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'catview'));
    }

    /**
     * function suredelcategory
     */
    public function suredelcategory()
    {
        // get parameters we need
        $cid = (int)$this->getPassedValue('cid', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_DELETE), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        $this->view->assign('cid', $cid);

        return $this->view->fetch('admin/suredelcategory.tpl');
    }

    /**
     * function delcategory
     */
    public function delcategory()
    {
        // get parameters we need
        $cid = (int)$this->getPassedValue('cid', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_DELETE), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // delete the category
        if (ModUtil::apiFunc('Weblinks', 'admin', 'delcategory', array('cid' => $cid))) {
            // Success
            $this->registerStatus($this->__('Category successfully deleted'));
        }

        // redirect to function catview
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'catview'));
    }

    /**
     * function linkview
     */
    public function linkview()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->view->assign('catnum', $this->entityManager->getRepository('Weblinks_Entity_Category')->getCount())
                ->assign('numrows', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount())
                ->assign('submitter', UserUtil::getVar('uname'))
                ->assign('submitteremail', UserUtil::getVar('email'));

        return $this->view->fetch('admin/linkview.tpl');
    }

    /**
     * function addlink
     */
    public function addlink()
    {
        // get parameters we need
        $link = $this->getPassedValue('link', array(), 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADD), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // VALIDATION
        if ($this->getVar('doubleurl') == 0) {
            // check if URL already exists
            $checkurl = count($this->entityManager->getRepository('Weblinks_Entity_Link')->findBy(array('url' => $link['url'], 'status' => Link::ACTIVE)));
            if ($checkurl > 0) {
                $this->registerError($this->__('Sorry! Please try again: this link is already listed in the database!'));
                $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
            }
        }
        /* Check if Title exists */
        if (empty($link['title'])) {
            $this->registerError($this->__('Sorry! Please try again: you need to specify a TITLE for your link!'));
            $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
        }
        /* Check if URL exists */
        if (empty($link['url'])) {
            $this->registerError($this->__('Sorry! Please try again: you need to specify a URL for your link!'));
            $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
        }
        // check hooked modules for validation
        $hook = new Zikula_ValidationHook('weblinks.ui_hooks.link.validate_edit', new Zikula_Hook_ValidationProviders());
        $hookvalidators = $this->notifyHooks($hook)->getValidators();
        if ($hookvalidators->hasErrors()) {
            $this->registerError($this->__('Error! Hooked content does not validate.'));
            $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
        }
       
        // add link to db
        $lid = ModUtil::apiFunc('Weblinks', 'admin', 'editlink', $link);
        if ($lid <= 0) {
            $this->registerError($this->__('Error! Could not add link to db.'));
            $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
        } else {
            $this->registerStatus($this->__('New link added to the database'));
        }

        // notify hooks
        $url = new Zikula_ModUrl('Weblinks', 'user', 'viewlinkdetails', ZLanguage::getLanguageCode(), array('lid' => $lid));
        $this->notifyHooks(new Zikula_ProcessHook('weblinks.ui_hooks.link.process_edit', $lid, $url));
        
        if ($link['new'] == 1) {
            // send email
            if (!empty($link['email'])) {
                $sitename = System::getVar('sitename');
                // $adminmail = System::getVar('adminmail');
                // $from = $adminmail; ??
                $subject = DataUtil::formatForDisplay($this->__('Your link at')) . " " . DataUtil::formatForDisplay($sitename);
                $message = DataUtil::formatForDisplay($this->__('Hello')) . " " . DataUtil::formatForDisplay($link['name']) . ",<br /><br />" . DataUtil::formatForDisplay($this->__("your link submission has been approved for the site's search engine.")) . "<br /><br />" . DataUtil::formatForDisplay($this->__('Link title'))
                        . ": " . DataUtil::formatForDisplay($link['title']) . "<br />" . DataUtil::formatForDisplay($this->__('URL')) . ": " . DataUtil::formatForDisplay($link['url']) . "<br />" . DataUtil::formatForDisplay($this->__('Description')) . ": " . DataUtil::formatForDisplayHTML($link['description']) . "<br /><br /><br />"
                        . DataUtil::formatForDisplay($this->__("The site's search engine is available at:")) . "<br /><a href='" . System::getBaseUrl() . "index.php?module=Weblinks'>" . System::getBaseUrl() . "index.php?module=Weblinks</a><br /><br />"
                        . DataUtil::formatForDisplay($this->__('Thank you for your submission!')) . "<br /><br />" . DataUtil::formatForDisplay($sitename) . " " . DataUtil::formatForDisplay($this->__('Team.')) . "";
                // send the e-mail
                ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array('toaddress' => $link['email'], 'subject' => $subject, 'body' => $message, 'html' => true));
            }
        }

        $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
    }

    /**
     * function modlink
     */
    public function modlink()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GETPOST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        // get linkarray from db
        $link = $this->entityManager->find('Weblinks_Entity_Link', $lid)->toArray();
        
        // check if $link return
        if (!isset($link)) {
            $this->registerError($this->__('No link found'));
            $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
        }

        $this->view->assign('link', $link);

        return $this->view->fetch('admin/modlink.tpl');
    }

    /**
     * function modlinks
     */
    public function modlinks()
    {
        // get parameters we need
        $link = $this->getPassedValue('link', array(), 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // update the link with new vars
        if (ModUtil::apiFunc('Weblinks', 'admin', 'updatelink', array(
            'lid' => $link['lid'],
            'cid' => $link['cat'],
            'title' => $link['title'],
            'url' => $link['url'],
            'description' => $link['description'],
            'name' => $link['name'],
            'email' => $link['email'],
            'hits' => $link['hits']))) {
            // Success
            $url = new Zikula_ModUrl('Weblinks', 'user', 'viewlinkdetails', ZLanguage::getLanguageCode(), array('lid' => $link['lid']));
            $this->notifyHooks(new Zikula_ProcessHook('weblinks.ui_hooks.link.process_edit', $link['lid'], $url));
            $this->registerStatus($this->__('Link successfully modified'));
        }

        // redirect to function linkview
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
    }

    /**
     * function dellink
     */
    public function dellink()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GET');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_DELETE), LogUtil::getErrorMsgPermission());

//        $this->checkCsrfToken();

        // delete the link
        $link = $this->entityManager->find('Weblinks_Entity_Link', $lid);
        if (isset($link)) {
            $this->entityManager->remove($link);
            $this->entityManager->flush();
            $this->registerStatus($this->__('Link removed from the database'));
            $this->notifyHooks(new Zikula_ProcessHook('weblinks.ui_hooks.link.process_delete', $lid));
        }

        // redirect to function linkview
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'linkview'));
    }

    /**
     * function delnewlink
     */
    public function delnewlink()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'GETPOST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_DELETE), LogUtil::getErrorMsgPermission());

//        $this->checkCsrfToken();

        // delete new link
        if (ModUtil::apiFunc('Weblinks', 'admin', 'delnewlink', array('lid' => $lid))) {
            // Success
            $this->registerStatus($this->__('New link removed from the database'));
            $this->notifyHooks(new Zikula_ProcessHook('weblinks.ui_hooks.link.process_delete', $lid));
        }

        // redirect to function view
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'view'));
    }

    /**
     * function validate
     */
    public function validate()
    {
        // get parameters we need
        $cid = (int)$this->getPassedValue('cid', null, 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // check links
        $links = ModUtil::apiFunc('Weblinks', 'admin', 'checklinks', array('cid' => $cid));

        $this->view->assign('cid', $cid)
                ->assign('links', $links);

        return $this->view->fetch('admin/validate.tpl');
    }

    /**
     * function listbrokenlinks
     */
    public function listbrokenlinks()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->view->assign('totalbrokenlinks', $this->entityManager->getRepository('Weblinks_Entity_Link')->getCount(Link::ACTIVE_BROKEN, '='))
                ->assign('brokenlinks', $this->entityManager->getRepository('Weblinks_Entity_Link')->findBy(array('status' => Link::ACTIVE_BROKEN)));

        return $this->view->fetch('admin/listbrokenlinks.tpl');
    }

    /**
     * function delbrokenlinks
     */
    public function delbrokenlinks()
    {
        // get parameters we need
        $rid = (int)$this->getPassedValue('rid', null, 'REQUEST');
        $lid = (int)$this->getPassedValue('lid', null, 'REQUEST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_DELETE), LogUtil::getErrorMsgPermission());

//        $this->checkCsrfToken();

        // del request
        ModUtil::apiFunc('Weblinks', 'admin', 'delrequest', array('rid' => $rid));

        // del link
        if (ModUtil::apiFunc('Weblinks', 'admin', 'dellink', array('lid' => $lid))) {
            // Success
            $this->registerStatus($this->__('Link removed from the database'));
            $this->notifyHooks(new Zikula_ProcessHook('weblinks.ui_hooks.link.process_delete', $lid));
        }

        // redirect to function listbrokenlinks
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'listbrokenlinks'));
    }

    /**
     * function ignorebrokenlinks
     */
    public function ignorebrokenlinks()
    {
        // get parameters we need
        $lid = (int)$this->getPassedValue('lid', null, 'REQUEST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

//        $this->checkCsrfToken();

        // change status of link
        $link = $this->entityManager->find('Weblinks_Entity_Link', $lid);
        $link->setStatus(Link::ACTIVE);
        $this->entityManager->flush();

        // redirect to function listbrokenlinks
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'listbrokenlinks'));
    }

    /**
     * function listmodrequests
     */
    public function listmodrequests()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->view->assign('totalmodrequests', ModUtil::apiFunc('Weblinks', 'admin', 'countmodrequests'))
                ->assign('modrequests', ModUtil::apiFunc('Weblinks', 'admin', 'modrequests'));

        return $this->view->fetch('admin/listmodrequests.tpl');
    }

    /**
     * function changemodrequests
     */
    public function changemodrequests()
    {
        // get parameters we need
        $rid = $this->getPassedValue('rid', null, 'REQUEST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // get vars from request
        $requestlink = ModUtil::apiFunc('Weblinks', 'admin', 'linkmodrequest', array('rid' => $rid));

        if ($requestlink) {
            // del request
            ModUtil::apiFunc('Weblinks', 'admin', 'delrequest', array('rid' => $rid));

            // change link
            if (ModUtil::apiFunc('Weblinks', 'admin', 'updatemodlink', array('lid' => $requestlink['lid'],
                        'cid' => $requestlink['cat_id'],
                        'title' => $requestlink['title'],
                        'url' => $requestlink['url'],
                        'description' => $requestlink['description']))) {

                // Success
                $this->registerStatus($this->__('Link was changed successfuly'));
            }
        }

        // redirect to function listmodrequests
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'listmodrequests'));
    }

    /**
     * function delmodrequests
     */
    public function delmodrequests()
    {
        // get parameters we need
        $rid = $this->getPassedValue('rid', null, 'REQUEST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // delete request
        if (ModUtil::apiFunc('Weblinks', 'admin', 'delrequest', array('rid' => $rid))) {
            // Success
            $this->registerStatus($this->__('User link modification requests was ignored'));
        }

        // redirect to function listmodrequests
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'listmodrequests'));
    }

    /**
     * function getconfig
     */
    public function getconfig()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        return $this->view->fetch('admin/getconfig.tpl');
    }

    /**
     * function updateconfig
     */
    public function updateconfig()
    {
        // get our input
        $config = $this->getPassedValue('config', array(), 'POST');

        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // Update module variables
        if (!isset($config['perpage']) || !is_numeric($config['perpage'])) {
            $config['perpage'] = 10;
        }
        if (!isset($config['newlinks']) || !is_numeric($config['newlinks'])) {
            $config['newlinks'] = 10;
        }
        if (!isset($config['bestlinks']) || !is_numeric($config['bestlinks'])) {
            $config['bestlinks'] = 10;
        }
        if (!isset($config['linksresults']) || !is_numeric($config['linksresults'])) {
            $config['linksresults'] = 10;
        }
        if (!isset($config['linksinblock']) || !is_numeric($config['linksinblock'])) {
            $config['linksinblock'] = 10;
        }
        if (!isset($config['popular']) || !is_numeric($config['popular'])) {
            $config['popular'] = 500;
        }
        if (!isset($config['mostpoplinkspercentrigger']) || !is_numeric($config['mostpoplinkspercentrigger'])) {
            $config['mostpoplinkspercentrigger'] = 0;
        }
        if (!isset($config['mostpoplinks']) || !is_numeric($config['mostpoplinks'])) {
            $config['mostpoplinks'] = 25;
        }
        if (!isset($config['featurebox']) || !is_numeric($config['featurebox'])) {
            $config['featurebox'] = 1;
        }
        if (!isset($config['targetblank']) || !is_numeric($config['targetblank'])) {
            $config['targetblank'] = 0;
        }
        if (!isset($config['doubleurl']) || !is_numeric($config['doubleurl'])) {
            $config['doubleurl'] = 0;
        }
        if (!isset($config['unregbroken']) || !is_numeric($config['unregbroken'])) {
            $config['unregbroken'] = 0;
        }
        if (!isset($config['blockunregmodify']) || !is_numeric($config['blockunregmodify'])) {
            $config['blockunregmodify'] = 0;
        }
        if (!isset($config['links_anonaddlinklock']) || !is_numeric($config['links_anonaddlinklock'])) {
            $config['links_anonaddlinklock'] = 0;
        }
        if (!isset($config['thumber']) || !is_numeric($config['thumber'])) {
            $config['thumber'] = 0;
        }
        if (!isset($config['thumbersize'])) {
            $config['thumbersize'] = 'XL';
        }
        
        $this->setVars($config);

        // the module configuration has been updated successfuly
        $this->registerStatus($this->__('Configuration updated'));

        // redirect to function getconfig
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'getconfig'));
    }

    /**
     * function import
     */
    public function help()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());

        return $this->view->fetch('admin/help.tpl');
    }

    /**
     * function import
     */
    public function import()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        $this->view->assign('ezcomments', ModUtil::available('EZComments'))
                ->assign('ratings', ModUtil::available('Ratings'))
                ->assign('cmodsweblinks', ModUtil::available('CmodsWebLinks'));

        return $this->view->fetch('admin/import.tpl');
    }

    /**
     * function importratings
     */
    public function importratings()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // Security check
        if (!SecurityUtil::checkPermission(0, 'Ratings::', "::", ACCESS_ADMIN)) {
            return $this->registerError($this->__('Weblinks migration: Not Admin'));
        }

        if (!ModUtil::available('Ratings')) {
            return $this->registerError($this->__('Ratings not available'));
        }

        ModUtil::dbInfoLoad('Ratings');
        $dbtable = DBUtil::getTables();
        $linkscolumn = $dbtable['links_links_column'];
        $where = "WHERE $linkscolumn[totalvotes] != '0'";
        $votes = DBUtil::selectObjectArray('links_links', $where);
        $counter = 0;
        $ratingtype = ModUtil::getVar('Ratings', 'defaultstyle');
        foreach ($votes as $v) {
            $obj = array(
                'module' => 'Weblinks',
                'itemid' => $v['lid'],
                'ratingtype' => $ratingtype,
                'rating' => ceil($v['linkratingsummary'] * 10),
                'numratings' => $v['totalvotes']);

            if (!DBUtil::insertObject($obj, 'ratings'))
                return $this->registerError($this->__('Error inserting votes in ratings table.'));
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s votes from Weblinks to Ratings', $counter));

        // redirect to function view
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'import'));
    }

    /**
     * function importezcomments
     */
    public function importezcomments()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        // Security check
        if (!SecurityUtil::checkPermission(0, 'EZComments::', "::", ACCESS_ADMIN)) {
            return $this->registerError($this->__('Weblinks migration: Not Admin'));
        }

        if (!ModUtil::available('EZComments')) {
            return $this->registerError($this->__('EZComments not available'));
        }

        ModUtil::dbInfoLoad('EZComments');
        $dbtable = DBUtil::getTables();
        $linkscolumn = $dbtable['links_votedata_column'];
        $where = "WHERE $linkscolumn[ratingcomments] != ''";
        $comments = DBUtil::selectObjectArray('links_votedata', $where);
        $counter = 0;

        foreach ($comments as $c) {
            $linkscolumn = $dbtable['links_links_column'];
            $where = "WHERE $linkscolumn[lid] = " . $c['ratinglid'];
            $user = DBUtil::selectObject('links_links', $where);
            if ($c['ratinguser'] == "Anonymous") {
                $c['ratinguser'] = $this->__('anonymous user');
            }
            $obj = array(
                'modname' => 'Weblinks',
                'objectid' => $c['ratinglid'],
                'url' => System::getBaseUrl() . 'index.php?module=Weblinks&func=viewlinkdetails&lid=' . $c['ratinglid'],
                'date' => $c['ratingtimestamp'],
                'uid' => UserUtil::getIdFromName($c['ratinguser']),
                'owneruid' => UserUtil::getIdFromName($user['submitter']),
                'comment' => $c['ratingcomments'],
                'subject' => '',
                'replyto' => -1,
                'ipaddr' => $c['ratinghostname']);
            if (!DBUtil::insertObject($obj, 'EZComments'))
                return $this->registerError($this->__('Error inserting comments in ezcomments table.'));
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s comments from Weblinks to EZComments', $counter));

        // redirect to function view
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'import'));
    }

    /**
     * function importcmodsweblinks
     */
    public function importcmodsweblinks()
    {
        // Security check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN), LogUtil::getErrorMsgPermission());

        $this->checkCsrfToken();

        ModUtil::dbInfoLoad('CmodsWebLinks');
        $dbtable = DBUtil::getTables();

        // import categories
        $table = $dbtable['cmodsweblinks_categories'];
        $sql = "SELECT * FROM $table";
        $categories = DBUtil::selectObjectArraySQL($sql, 'cmodsweblinks_categories');
        //     $categories = DBUtil::selectObjectArray('cmodsweblinks_categories', '', 'cat_id', '-1', '-1');
        $counter = 0;
        foreach ($categories as $category) {
            $obj = array(
                'parent_id' => $category['parent_id'],
                'title' => $category['title'],
                'cdescription' => $category['cdescription']);
            if (!DBUtil::insertObject($obj, 'links_categories', 'cat_id'))
                return $this->registerError($this->__('Error inserting CmodsWebLinks categories in Weblinks categories table.'));
            // get cat_id from the new category
            $remembercat[$category['cat_id']] = DBUtil::getInsertID('links_categories', 'cat_id');
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s categories from CmodsWebLinks to Weblinks', $counter));

        // import links
        $table = $dbtable['cmodsweblinks_links'];
        $sql = "SELECT * FROM $table";
        $links = DBUtil::selectObjectArraySQL($sql, 'cmodsweblinks_links');
        //    $links = DBUtil::selectObjectArray('cmodsweblinks_links', '', 'lid', '-1', '-1');
        $counter = 0;
        foreach ($links as $link) {
            $obj = array(
                'cat_id' => $remembercat[$link['cat_id']],
                'title' => $link['title'],
                'url' => $link['url'],
                'description' => $link['description'],
                'date' => $link['date'],
                'name' => $link['name'],
                'email' => $link['email'],
                'hits' => $link['hits'],
                'submitter' => $link['submitter'],
                'linkratingsummary' => $link['linkratingsummary'],
                'totalvotes' => $link['totalvotes'],
                'totalcomments' => $link['totalcomments']);
            if (!DBUtil::insertObject($obj, 'links_links', 'lid'))
                return $this->registerError($this->__('Error inserting CmodsWebLinks links in Weblinks links table.'));
            // get lid from the new link
            $rememberlink[$link['lid']] = DBUtil::getInsertID('links_links', 'lid');
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s links from CmodsWebLinks to Weblinks', $counter));

        // import modrequests
        $table = $dbtable['cmodsweblinks_modrequest'];
        $sql = "SELECT * FROM $table";
        $modrequests = DBUtil::selectObjectArraySQL($sql, 'cmodsweblinks_modrequest');
        //    $modrequests = DBUtil::selectObjectArray('cmodsweblinks_modrequest', '', 'requestid', '-1', '-1');
        $counter = 0;
        foreach ($modrequests as $modrequest) {
            $obj = array(
                'lid' => $rememberlink[$link['lid']],
                'cat_id' => $remembercat[$modrequest['cat_id']],
                'sid' => $modrequest['sid'],
                'title' => $modrequest['title'],
                'url' => $modrequest['url'],
                'description' => $modrequest['description'],
                'modifysubmitter' => $modrequest['modifysubmitter'],
                'brokenlink' => $modrequest['brokenlink']);
            if (!DBUtil::insertObject($obj, 'links_modrequest', 'requestid'))
                return $this->registerError($this->__('Error inserting CmodsWebLinks modrequests in Weblinks modrequests table.'));
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s modrequests from CmodsWebLinks to Weblinks', $counter));

        // import newlinks
        $table = $dbtable['cmodsweblinks_newlink'];
        $sql = "SELECT * FROM $table";
        $newlinks = DBUtil::selectObjectArraySQL($sql, 'cmodsweblinks_newlink');
        //    $newlinks = DBUtil::selectObjectArray('cmodsweblinks_newlink', '', 'lid', '-1', '-1');
        $counter = 0;
        foreach ($newlinks as $newlink) {
            $obj = array(
                'cat_id' => $remembercat[$newlink['cat_id']],
                'title' => $newlink['title'],
                'url' => $newlink['url'],
                'description' => $newlink['description'],
                'name' => $newlink['name'],
                'email' => $newlink['email'],
                'submitter' => $newlink['submitter']);
            if (!DBUtil::insertObject($obj, 'links_newlink', 'lid'))
                return $this->registerError($this->__('Error inserting CmodsWebLinks newlinks in Weblinks newlinks table.'));
            $counter++;
        }
        $this->registerStatus($this->__f('migrated: %s newlinks from CmodsWebLinks to Weblinks', $counter));

        // redirect to function view
        $this->redirect(ModUtil::url('Weblinks', 'admin', 'import'));
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