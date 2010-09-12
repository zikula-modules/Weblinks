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
 * function main
 */
function Weblinks_user_main() // ready
{
    return Weblinks_user_view();
}

/**
 * function view
 */
function Weblinks_user_view() // ready
{
    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // get all categories
    $categories = pnModAPIFunc('Weblinks', 'user', 'categories');

    // value of the function is checked
    if (!$categories) {
        return DataUtil::formatForDisplayHTML(_WL_NOCATS);
    }

    // create output
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('categories', $categories);
    $pnRender->assign('numrows', pnModAPIFunc('Weblinks', 'user', 'numrows'));
    $pnRender->assign('catnum', pnModAPIFunc('Weblinks', 'user', 'catnum'));
    if (pnModGetVar('Weblinks', 'featurebox') == 1) {
        $pnRender->assign('linkbox', pnModGetVar('Weblinks', 'featurebox'));
        $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));
        $pnRender->assign('blocklast', pnModAPIFunc('Weblinks', 'user', 'lastweblinks'));
        $pnRender->assign('blockmostpop', pnModAPIFunc('Weblinks', 'user', 'mostpopularweblinks'));
    }

    // return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_view.html');
}

/**
 * function category
 */
function Weblinks_user_category() // ready
{
    // get parameters we need
    $cid = (int)FormUtil::getPassedValue('cid', null, 'GET');
    $orderby = FormUtil::getPassedValue('orderby', 'titleA', 'GET');
    $startnum = (int)FormUtil::getPassedValue('startnum', 1, 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::$cid", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // get category vars
    $category = pnModAPIFunc('Weblinks', 'user', 'category', array('cid' => $cid));

    // get subcategories in this category
    $subcategory = pnModAPIFunc('Weblinks', 'user', 'subcategory', array('cid' => $cid));

    // get links in this category
    $weblinks = pnModAPIFunc('Weblinks', 'user', 'weblinks', array('cid' => $cid,
                                                                   'orderbysql' => pnModAPIFunc('Weblinks', 'user', 'orderby', array('orderby' => $orderby)),
                                                                   'startnum' => $startnum,
                                                                   'numlinks' => pnModGetVar('Weblinks', 'perpage')));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('orderby', $orderby);
    $pnRender->assign('category', $category);
    $pnRender->assign('subcategory', $subcategory);
    $pnRender->assign('weblinks', $weblinks);
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));
    $pnRender->assign('wlpager', array('numitems' => pnModAPIFunc('Weblinks', 'user', 'countcatlinks', array('cid' => $cid)),
                                       'itemsperpage' => pnModGetVar('Weblinks', 'perpage')));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_category.html');
}

/**
 * function visit
 */
function Weblinks_user_visit() // ready
{
    // get parameters we need
    $lid = (int)FormUtil::getPassedValue('lid', null, 'GET');

    // get link
    $link = pnModAPIFunc('Weblinks', 'user', 'link', array('lid' => $lid));

    // the return value of the function is checked here
    if ($link == false) {
        return LogUtil::registerError(_WL_NOSUCHLINK);
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::$link[cat_id]", ACCESS_READ)) {
        return LogUtil::registerError (_MODULENOAUTH);
        pnRedirect(pnModURL('Weblinks', 'user', 'view'));
    } else {
        // set the counter for the link +1
        pnModAPIFunc('Weblinks', 'user', 'hitcountinc', array('lid' => $lid, 'hits' => $link['hits']));

        // is the URL local?
        if (eregi('^http:|^ftp:|^https:', $link['url'])) {
            pnRedirect($link['url']);
        } else {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $link['url']);
        }
    }

    // return
    return true;
}

/**
 * function search
 */
function Weblinks_user_search() // ready
{
    // get parameters we need
    $query = FormUtil::getPassedValue('query', null, 'GETPOST');
    $orderby = FormUtil::getPassedValue('orderby', 'titleA', 'GETPOST');
    $startnum = (int)FormUtil::getPassedValue('startnum', 1, 'GETPOST');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // get categories with $query inside
    $categories = pnModAPIFunc('Weblinks', 'user', 'searchcats', array('query' => $query));

    // get weblinks with $query inside
    $weblinks = pnModAPIFunc('Weblinks', 'user', 'search_weblinks', array('query' => $query,
                                                                          'orderbysql' => pnModAPIFunc('Weblinks', 'user', 'orderby', array('orderby' =>$orderby)),
                                                                          'startnum' => $startnum,
                                                                          'numlinks' => pnModGetVar('Weblinks', 'linksresults')));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('query', $query);
    $pnRender->assign('categories', $categories);
    $pnRender->assign('orderby', $orderby);
    $pnRender->assign('startnum', $startnum);
    $pnRender->assign('weblinks', $weblinks);
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));
    $pnRender->assign('wlpager', array('numlinks' => pnModAPIFunc('Weblinks', 'user', 'countsearchlinks', array('query' => $query)),
                                       'itemsperpage' => pnModGetVar('Weblinks', 'linksresults')));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_searchresults.html');
}

/**
 * function randomlink
 */
function Weblinks_user_randomlink() // ready
{
    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // get random link id an redirect to the visit function
    pnRedirect(pnModURL('Weblinks', 'user', 'visit', array('lid' => pnModAPIFunc('Weblinks', 'user', 'random'))));

    return true;
}

/**
 * function viewlinkdetails
 */
function Weblinks_user_viewlinkdetails() // ready
{
    // get parameters we need
    $lid = (int)FormUtil::getPassedValue('lid', null, 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // get link details
    $weblink = pnModAPIFunc('Weblinks', 'user', 'link', array('lid' => $lid));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));
    $pnRender->assign('weblink', $weblink);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_details.html');
}

/**
 * function newlinks
 */
function Weblinks_user_newlinks() // ready
{
    // get parameters we need
    $newlinkshowdays = (int)FormUtil::getPassedValue('newlinkshowdays', '7', 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('newlinkshowdays', $newlinkshowdays);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_newlinks.html');
}

/**
 * function newlinksdate
 */
function Weblinks_user_newlinksdate() // ready
{
    // get parameters we need
    $selectdate = (int)FormUtil::getPassedValue('selectdate', null, 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // count weblinks from the selected day
    $totallinks = pnModAPIFunc('Weblinks', 'user', 'totallinks', array('selectdate' => $selectdate));

    // get weblinks from the selected day
    $weblinks = pnModAPIFunc('Weblinks', 'user', 'weblinksbydate', array('selectdate' => $selectdate));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('dateview', (ml_ftime(__('%b %d, %Y'), $selectdate)));
    $pnRender->assign('totallinks', $totallinks);
    $pnRender->assign('weblinks', $weblinks);
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_newlinksdate.html');
}

/**
 * function mostpopular
 */
function Weblinks_user_mostpopular() // ready
{
    // get parameters we need
    $ratenum = (int)FormUtil::getPassedValue('ratenum', null, 'GET');
    $ratetype = FormUtil::getPassedValue('ratetype', null, 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    $mostpoplinkspercentrigger = pnModGetVar('Weblinks', 'mostpoplinkspercentrigger');
    $mostpoplinks = pnModGetVar('Weblinks', 'mostpoplinks');
    $mainvotedecimal = pnModGetVar('Weblinks', 'mainvotedecimal');

    if ($ratenum != "" && $ratetype != "") {
        if (!is_numeric($ratenum)) {
            $ratenum=5;
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
        $totalmostpoplinks = pnModAPIFunc('Weblinks', 'user', 'numrows');
        $mostpoplinks = $mostpoplinks / 100;
        $mostpoplinks = $totalmostpoplinks * $mostpoplinks;
        $mostpoplinks = round($mostpoplinks);
        $mostpoplinks = max(1, $mostpoplinks);
    }

    // get most popular weblinks
    $weblinks = pnModAPIFunc('Weblinks', 'user', 'mostpopularweblinks', array('mostpoplinks' => $mostpoplinks));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('mostpoplinkspercentrigger', $mostpoplinkspercentrigger);
    $pnRender->assign('toplinkspercent', $toplinkspercent);
    $pnRender->assign('totalmostpoplinks', $totalmostpoplinks);
    $pnRender->assign('mostpoplinks', $mostpoplinks);
    $pnRender->assign('weblinks', $weblinks);
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_mostpopular.html');
}
/**
 * function brockenlink
 */
function Weblinks_user_brokenlink() // ready
{
    // get parameters we need
    $lid = (int)FormUtil::getPassedValue('lid', null, 'GET');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    if (pnUserLoggedIn()) {
        $submitter = pnUserGetVar('uname');
    } else {
        $submitter = pnConfigGetVar("anonymous");
    }

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('authid', SecurityUtil::generateAuthKey('Weblinks'));
    $pnRender->assign('lid', $lid);
    $pnRender->assign('submitter', $submitter);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_brokenlink.html');
}

/**
 * function brockenlinks
 */
function Weblinks_user_brokenlinks() // ready
{
    // get parameters we need
    $lid = (int)FormUtil::getPassedValue('lid', null, 'POST');
    $submitter = FormUtil::getPassedValue('submitter', null, 'POST');

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    // Confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError (pnModURL('Weblinks', 'user', 'view'));
    }

    // add broken link
    pnModAPIFunc('Weblinks', 'user', 'addbrockenlink', array('lid' => $lid, 'submitter' => $submitter));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_brokenlinks.html');
}

/**
 * function modifylinkrequest
 */
function Weblinks_user_modifylinkrequest() // ready
{
    // get parameters we need
    $lid = (int)FormUtil::getPassedValue('lid', null, 'GET');

    // Security check
    if (!pnModGetVar('Weblinks', 'blockunregmodify') == 0 && 
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }


    // get link vars
    $link = pnModAPIFunc('Weblinks', 'user', 'link', array('lid' => $lid));

    if (pnUserLoggedIn()) {
        $submitter = pnUserGetVar('uname');
    } else {
        $submitter = pnConfigGetVar("anonymous");
    }

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('blockunregmodify', pnModGetVar('Weblinks', 'blockunregmodify'));
    $pnRender->assign('link', $link);
    $pnRender->assign('submitter', $submitter);
    $pnRender->assign('anonymous', pnConfigGetVar("anonymous"));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_modifylinkrequest.html');
}

/**
 * function modifylinkrequests
 */
function Weblinks_user_modifylinkrequests() // ready
{
    // get parameters we need
    $modlink = FormUtil::getPassedValue('modlink', array(), 'POST');

    // Security check
    if (!pnModGetVar('Weblinks', 'blockunregmodify') == 0 && 
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    // Confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError (pnModURL('Weblinks', 'user', 'view'));
    }
    
    // add link request
    pnModAPIFunc('Weblinks', 'user', 'modifylinkrequest', array('lid' => $modlink['lid'],
                                                                'cid' => $modlink['cid'],
                                                                'title' => $modlink['title'],
                                                                'url' => $modlink['url'],
                                                                'description' => $modlink['description'],
                                                                'submitter' => $modlink['submitter']));
    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_modifylinkrequests.html');
}

/**
 * function addlink
 */
function Weblinks_user_addlink() // ready
{
    // Security check
    if (!pnModGetVar('Weblinks', 'links_anonaddlinklock') == 0 && 
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            $addlink = false;
    } else {
        $addlink = true;
    }

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('authid', SecurityUtil::generateAuthKey('Weblinks'));
    $pnRender->assign('addlink', $addlink);
    if (pnUserLoggedIn()) {
        $pnRender->assign('submitter', pnUserGetVar('uname'));
        $pnRender->assign('submitteremail', pnUserGetVar('email'));
    }

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_addlink.html');
}

/**
 * function add
 */
function Weblinks_user_add() // ready
{
    // get parameters we need
    $newlink = FormUtil::getPassedValue('newlink', array(), 'POST');

    // Security check
    if (!pnModGetVar('Weblinks', 'links_anonaddlinklock') == 0 && 
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    // Confirm authorisation code
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError (pnModURL('Weblinks', 'user', 'view'));
    }

    // write the link to db and get a status message back
    $link = pnModAPIFunc('Weblinks', 'user', 'add', array('title' => $newlink['title'],
                                                          'url' => $newlink['url'],
                                                          'cid' => $newlink['cid'],
                                                          'description' => $newlink['description'],
                                                          'submitter' => $newlink['submitter'],
                                                          'submitteremail' => $newlink['submitteremail']));

    // create output object
    $pnRender = & pnRender::getInstance('Weblinks', false);

    // assign various useful template variables
    $pnRender->assign('submit', $link['submit']);
    $pnRender->assign('text', $link['text']);

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_user_add.html');
}