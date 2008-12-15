<?php
/**
 * Zikula Application Framework
 *
 * Web_Links
 *
 * @version $Id$
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function Web_Links_admin_main() // fertig
{
    return Web_Links_admin_view();
}

function Web_Links_admin_view()
{
    // Security check
    if ((!SecurityUtil::checkPermission('Web_Links::Category', '::', ACCESS_EDIT)) &&
        (!SecurityUtil::checkPermission('Web_Links::Link', '::', ACCESS_EDIT))) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('numrows', pnModAPIFunc('Web_Links', 'user', 'numrows'));
    $pnRender->assign('catnum', pnModAPIFunc('Web_Links', 'user', 'catnum'));
    // Status
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $result =& $dbconn->Execute("SELECT COUNT(*) FROM $pntable[links_modrequest] WHERE $column[brokenlink]='1'");

    list($totalbrokenlinks) = $result->fields;
    $result =& $dbconn->Execute("SELECT COUNT(*) FROM $pntable[links_modrequest] WHERE $column[brokenlink]='0'");

    list($totalmodrequests) = $result->fields;

    $pnRender->assign('authid', pnSecGenAuthKey());
    $pnRender->assign('totalbrokenlinks', $totalbrokenlinks);
    $pnRender->assign('totalmodrequests', $totalmodrequests);
    $pnRender->assign('newweblinks', pnModAPIFunc('Web_Links', 'admin', 'newweblinks'));
    $pnRender->assign('authid', pnSecGenAuthKey());

    return $pnRender->fetch('weblinks_admin_view.html');
}

function Web_Links_admin_catview() // fertig
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Category', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('catnum', pnModAPIFunc('Web_Links', 'user', 'catnum'));

    return $pnRender->fetch('weblinks_admin_catview.html');
}

function Web_Links_admin_addcategory() // geht
{
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');
    $title = FormUtil::getPassedValue('title', isset($args['title']) ? $args['title'] : null, 'POST');
    $cdescription = FormUtil::getPassedValue('cdescription', isset($args['cdescription']) ? $args['cdescription'] : null, 'POST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    if (!isset($cid) || !is_numeric($cid) || empty($title)) {
        return LogUtil::registerError(_MODARGSERROR);
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    if (!SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_categories_column'];
    $sql = "SELECT $column[cat_id] FROM $pntable[links_categories]
           WHERE $column[title]='".DataUtil::formatForStore($title)."'
           AND $column[parent_id]='".(int)DataUtil::formatForStore($cid)."'";
    $result =& $dbconn->Execute($sql);

    if (!$result->EOF) {
        LogUtil::registerError (_WL_ERRORTHECATEGORY);
        return pnRedirect(pnModURL('Web_Links', 'admin', 'catview'));
    }
    $column = &$pntable['links_categories_column'];
    $nextid = $dbconn->GenId($pntable['links_categories']);
    $sql = "INSERT INTO $pntable[links_categories] ($column[cat_id], $column[parent_id], $column[title], $column[cdescription])
            VALUES ($nextid, ".(int)DataUtil::formatForStore($cid).", '".DataUtil::formatForStore($title)."', '".DataUtil::formatForStore($cdescription)."')";
    $dbconn->Execute($sql);

    // the module configuration has been updated successfuly
    LogUtil::registerStatus (_WL_ADDCATEGORYSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'catview'));
}

function Web_Links_admin_modcategory() //fertig
{
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Category', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $category = pnModAPIFunc('Web_Links', 'admin', 'linksmodcat', array('cid' => $cid));

    $pnRender->assign('category', $category);

    return $pnRender->fetch('weblinks_admin_modcategory.html');
}

function Web_Links_admin_savemodcategory() // geht
{
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');
    $title = FormUtil::getPassedValue('title', isset($args['title']) ? $args['title'] : null, 'POST');
    $cdescription = FormUtil::getPassedValue('cdescription', isset($args['cdescription']) ? $args['cdescription'] : null, 'POST');

    if (!isset($cid) || !is_numeric($cid)){
        return LogUtil::registerError(_MODARGSERROR);
    }

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $catcolumn[title]
            FROM $cattable
            WHERE $catcolumn[cat_id] = '".(int)DataUtil::formatForStore($cid)."'";
    $result =& $dbconn->Execute($sql);

    list($oldtitle) = $result->fields;

    if (!pnSecAuthAction(0, 'Web Links::Category', "$oldtitle::$cid", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_categories_column'];
    $sql = "UPDATE $pntable[links_categories]
            SET $column[title]='".DataUtil::formatForStore($title)."', $column[cdescription]='".DataUtil::formatForStore($cdescription)."'
            WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
    $dbconn->Execute($sql);

    // the category has been modifyed
    LogUtil::registerStatus (_WL_MODIFYCATSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'catview'));
}


function Web_Links_admin_suredelcategory() // fertig
{
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');

    if (!isset($cid) || !is_numeric($cid)){
        return LogUtil::registerError(_MODARGSERROR);
    }

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('cid', $cid);

    return $pnRender->fetch('weblinks_admin_suredelcategory.html');
}

function Web_Links_admin_delcategory() // geht
{
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');

    if (!isset($cid) || !is_numeric($cid)){
        return LogUtil::registerError(_MODARGSERROR);
    }

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $catcolumn[title]
            FROM $cattable
            WHERE $catcolumn[cat_id] = '".(int)DataUtil::formatForStore($cid)."'";
    $result =& $dbconn->Execute($sql);

    list($oldtitle) = $result->fields;

    if (!SecurityUtil::checkPermission('Web_Links::Category', "$oldtitle::$cid", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

        $column = &$pntable['links_categories_column'];
        // delete category - könnte probleme geben mit unterkategorien
        $sql = "DELETE FROM $pntable[links_categories]
                WHERE $column[cat_id] = '".(int)DataUtil::formatForStore($cid)."'";
        $dbconn->Execute($sql);
        //delete subcategories
        $sql = "DELETE FROM $pntable[links_categories]
                WHERE '".(int)DataUtil::formatForStore($cid)."' = $column[parent_id]";
        $dbconn->Execute($sql);
        // delete links
        $column = &$pntable['links_links_column'];
        $sql = "DELETE FROM $pntable[links_links]
                WHERE $column[cat_id] = '".(int)DataUtil::formatForStore($cid)."'";
        $dbconn->Execute($sql);

        // the cat has been deleted
        LogUtil::registerStatus (_WL_DELCATSUCCESSFULY);

        return pnRedirect(pnModURL('Web_Links', 'admin', 'catview'));
}

function Web_Links_admin_linkview() // fertig
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('catnum', pnModAPIFunc('Web_Links', 'user', 'catnum'));
    $pnRender->assign('numrows', pnModAPIFunc('Web_Links', 'user', 'numrows'));
    if (pnUserLoggedIn()) {
        $pnRender->assign('submitter', pnUserGetVar('uname'));
    }

    return $pnRender->fetch('weblinks_admin_linkview.html');
}

function Web_Links_admin_addlink() //geht
{
    $link = FormUtil::getPassedValue('link', isset($args['link']) ? $args['link'] : null, 'POST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $sitename = pnConfigGetVar('sitename');
    $adminmail = pnConfigGetVar('adminmail');

    if (!SecurityUtil::checkPermission('Web_Links::Link', "::", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_links_column'];
    $sql = "SELECT COUNT(*) FROM $pntable[links_links]
            WHERE $column[url]='".DataUtil::formatForStore($link['url'])."'";
    $result =& $dbconn->Execute($sql);

    list($numrows) = $result->fields;
    if ($numrows>0) {
        LogUtil::registerError (_WL_ERRORURLEXIST);
        return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
    } else {
        /* Check if Title exist */
        if (empty($link['title'])) {
            LogUtil::registerError (_WL_ERRORNOTITLE);
            return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
        }
        /* Check if URL exist */
        if (empty($link['url'])) {
            LogUtil::registerError (_WL_ERRORNOURL);
            return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
        }
/*        // Check if Description exist
        if (empty($link['description'])) {
            LogUtil::registerError (_WL_ERRORNODESCRIPTION);
            return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
        }
*/
        $column = &$pntable['links_links_column'];
        $nextid = $dbconn->GenId($pntable['links_links']);
        $dbconn->Execute("INSERT INTO $pntable[links_links] ($column[lid], $column[cat_id],
                            $column[title], $column[url], $column[description], $column[date], $column[name],
                            $column[email], $column[hits], $column[submitter], $column[linkratingsummary],
                            $column[totalvotes], $column[totalcomments])
                            VALUES ('".DataUtil::formatForStore($nextid)."', ".(int)DataUtil::formatForStore($link['cat']).", '".DataUtil::formatForStore($link['title'])."',
                            '".DataUtil::formatForStore($link['url'])."', '".DataUtil::formatForStore($link['description'])."', now(), '".DataUtil::formatForStore($link['name'])."', '".DataUtil::formatForStore($link['email'])."', '0','".DataUtil::formatForStore($link['submitter'])."',0,0,0)");

        // Let any hooks know that we have created a new item.
        pnModCallHooks('item', 'display', $link['lid'], array('module' => 'Web_Links'));

        if ($link['new']==1) {
            $column = &$pntable['links_newlink_column'];
            $dbconn->Execute("DELETE FROM $pntable[links_newlink] WHERE $column[lid]='".(int)DataUtil::formatForStore($link['lid'])."'");
            if ($link['email']=="") {
            } else {
                // $from = $adminmail; ??
                $subject = _WL_YOURLINKAT." ".DataUtil::formatForDisplay($sitename);
                $message = _WL_HELLO." ".DataUtil::formatForDisplay($link['name']).":\n\n"._WL_WEAPPROVED."\n\n"._WL_LINKTITLE
                .": ".DataUtil::formatForDisplay($link['title'])."\n"._WL_URL.": ".DataUtil::formatForDisplay($link['url'])."\n"._WL_DESCRIPTION.": ".DataUtil::formatForDisplayHTML($link['description'])."\n\n\n"
                ._WL_YOUCANBROWSEUS. " " .pnGetBaseURL() . "index.php?module=Web_Links\n\n"
                ._WL_THANKS4YOURSUBMISSION."\n\n".DataUtil::formatForDisplay($sitename)." "._WL_TEAM."";
                // send the e-mail
                pnModAPIFunc('Mailer', 'user', 'sendmessage', array('toaddress' => $email, 'subject' => $subject, 'body' => $message));
            }

            LogUtil::registerStatus (_WL_NEWLINKADDED);
            return pnRedirect(pnModURL('Web_Links', 'admin', 'view'));
        }

        LogUtil::registerStatus (_WL_NEWLINKADDED);
        return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
    }
}

function Web_Links_admin_delnew() // geht
{
    $lid = (int)FormUtil::getPassedValue('lid', isset($args['lid']) ? $args['lid'] : null, 'GETPOST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    if (!SecurityUtil::checkPermission('Web_Links::Link', "::$lid", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_newlink_column'];
    $sql = "DELETE FROM $pntable[links_newlink]
            WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    $dbconn->Execute($sql);

    // the link has been deleted successfuly
    LogUtil::registerStatus (_WL_NEWLINKDELETED);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'view'));
}

function Web_Links_admin_linkcheck() // fertig
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    return $pnRender->fetch('weblinks_admin_linkcheck.html');
}

function Web_Links_admin_validate()
{
    // Get parameters from whatever input we need
    $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : null, 'POST');

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $catcolumn = &$pntable['links_categories_column'];
    $sql = "SELECT $catcolumn[title]
            FROM $pntable[links_categories]
            WHERE $catcolumn[cat_id] = '".(int)DataUtil::formatForStore($cid)."'";
    $result =& $dbconn->Execute($sql);

    list($cattitle) = $result->fields;

    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle::$lid", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('cid', $cid);

    /* Check ALL Links */
    if ($cid==0) {
        $column = &$pntable['links_links_column'];
        $sql = "SELECT $column[lid], $column[title], $column[url], $column[name], $column[email], $column[submitter]
                FROM $pntable[links_links]
                ORDER BY $column[title]";
        $result =& $dbconn->Execute($sql);
    }

    /* Check Categories */
    if ($cid!=0) {
        $column = &$pntable['links_categories_column'];
        $sql = "SELECT $column[title]
                FROM $pntable[links_categories]
                WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
        $result =& $dbconn->Execute($sql);

        list($transfertitle) = $result->fields;
        $pnRender->assign('transfertitle', $transfertitle);

        $column = &$pntable['links_links_column'];
        $sql = "SELECT $column[lid], $column[title], $column[url], $column[name], $column[email], $column[submitter]
                FROM $pntable[links_links]
                WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
        $result =& $dbconn->Execute($sql);
    }

    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($lid, $title, $url, $name, $email, $submitter) = $result->fields;

        if ($url == 'http://' OR $url == '' ) {
            $fp = false;
        } else {
            $vurl = parse_url($url);
            $fp = fsockopen($vurl['host'], 80, $errno, $errstr, 15);
          }

    $links[] = array('lid' => $lid,
                        'title' => $title,
                     'url' => $url,
                        'name' => $name,
                        'email' => $email,
                        'submitter' => $submitter,
                        'fp' => $fp);

    }

    $pnRender->assign('links', $links);
    $pnRender->assign('authid', pnSecGenAuthKey());

    return $pnRender->fetch('weblinks_admin_validate.html');
}

function Web_Links_admin_listbrokenlinks()
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[requestid], $column[lid], $column[modifysubmitter]
            FROM $pntable[links_modrequest]
            WHERE $column[brokenlink]='1'
            ORDER BY $column[requestid]";
    $result =& $dbconn->Execute($sql);

    $totalbrokenlinks = $result->PO_RecordCount();

    $pnRender->assign('totalbrokenlinks', $totalbrokenlinks);
    $pnRender->assign('brokenlinks', pnModAPIFunc('Web_Links', 'admin', 'brokenlinks'));
    $pnRender->assign('authid', pnSecGenAuthKey());

    return $pnRender->fetch('weblinks_admin_listbrokenlinks.html');
}

function Web_Links_admin_delbrokenlinks()
{
    $lid = (int)FormUtil::getPassedValue('lid', isset($args['lid']) ? $args['lid'] : null, 'REQUEST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $linkcolumn = &$pntable['links_links_column'];
    $linktable = $pntable['links_links'];
    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $linkcolumn[title], $catcolumn[title]
            FROM $linktable, $cattable
            WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $linkcolumn[cat_id] = $catcolumn[cat_id]";
    $result =& $dbconn->Execute($sql);

    list($title, $cattitle) = $result->fields;

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$title:$lid", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_modrequest_column'];
    $sql = "DELETE FROM $pntable[links_modrequest]
            WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    $dbconn->Execute($sql);
    $column = &$pntable['links_links_column'];
    $sql = "DELETE FROM $pntable[links_links]
            WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    $dbconn->Execute($sql);

    // the link has been deleted successfuly
    LogUtil::registerStatus (_WL_DELLINKSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'listbrokenlinks'));
}

function Web_Links_admin_ignorebrokenlinks()
{
    $lid = (int)FormUtil::getPassedValue('lid', isset($args['lid']) ? $args['lid'] : null, 'REQUEST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $linkcolumn = &$pntable['links_links_column'];
    $linktable = $pntable['links_links'];
    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $linkcolumn[title], $catcolumn[title]
            FROM $linktable, $cattable
            WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $linkcolumn[cat_id] = $catcolumn[cat_id])";
    $result =& $dbconn->Execute($sql);

    list($title, $cattitle) = $result->fields;

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$title:$lid", ACCESS_MODERATE)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_modrequest_column'];
    $sql = "DELETE FROM $pntable[links_modrequest]
            WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'
            AND $column[brokenlink]='1'";
    $dbconn->Execute($sql);

    // the link has been ignored successfuly
    LogUtil::registerStatus (_WL_IGNORELINKSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'listbrokenlinks'));
}

function Web_Links_admin_listmodrequests()
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender =& new pnRender('Web_Links');

    $totalmodrequests = pnModAPIFunc('Web_Links', 'admin', 'totalmodrequests');

    $pnRender->assign('totalmodrequests', $totalmodrequests);

    $modrequests = pnModAPIFunc('Web_Links', 'admin', 'modrequests');

    $pnRender->assign('modrequests', $modrequests);

    $pnRender->assign('authid', pnSecGenAuthKey());

    return $pnRender->fetch('weblinks_admin_listmodrequests.html');
}

function Web_Links_admin_changemodrequests()
{
    $requestid = FormUtil::getPassedValue('requestid', isset($args['requestid']) ? $args['requestid'] : null, 'REQUEST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[requestid], $column[lid], $column[cat_id], $column[title], $column[url], $column[description]
            FROM $pntable[links_modrequest]
            WHERE $column[requestid]='".(int)DataUtil::formatForStore($requestid)."'";
    $result =& $dbconn->Execute($sql);

    for (; !$result->EOF; $result->MoveNext()) {
        list($requestid, $lid, $cid, $modtitle, $url, $description)=$result->fields;

        $linkcolumn = &$pntable['links_links_column'];
        $linktable = $pntable['links_links'];
        $catcolumn = &$pntable['links_categories_column'];
        $cattable = $pntable['links_categories'];
        $sql = "SELECT $linkcolumn[title], $catcolumn[title]
                FROM $linktable, $cattable
                WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($lid)."'
                AND $linkcolumn[cat_id] = $catcolumn[cat_id]";
        $result2 =& $dbconn->Execute($sql);

        list($title, $cattitle) = $result2->fields;

        // Security check
        if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$title:$lid", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $column = &$pntable['links_links_column'];
        $sql = "UPDATE $pntable[links_links]
                SET $column[cat_id]=".(int)DataUtil::formatForStore($cid).", $column[title]='".DataUtil::formatForStore($modtitle)."',
                    $column[url]='".DataUtil::formatForStore($url)."', $column[description]='".DataUtil::formatForStore($description)."'
                WHERE $column[lid] = '".(int)DataUtil::formatForStore($lid)."'";
        $dbconn->Execute($sql);
        $column = &$pntable['links_modrequest_column'];
        $sql = "DELETE FROM $pntable[links_modrequest]
                WHERE $column[requestid]='".(int)DataUtil::formatForStore($requestid)."'";
        $dbconn->Execute($sql);
    }

    // the link has been changed successfuly
    LogUtil::registerStatus (_WL_CHANGELINKSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'listmodrequests'));
}

function Web_Links_admin_changeignorerequests()
{
    $requestid = FormUtil::getPassedValue('requestid', isset($args['requestid']) ? $args['requestid'] : null, 'REQUEST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[lid]
            FROM $pntable[links_modrequest]
            WHERE $column[requestid]='".(int)DataUtil::formatForStore($requestid)."'";
    $result =& $dbconn->Execute($sql);

    list($lid) = $result->fields;

    $linkcolumn = &$pntable['links_links_column'];
    $linktable = $pntable['links_links'];
    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $linkcolumn[title], $catcolumn[title]
            FROM $linktable, $cattable
            WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $linkcolumn[cat_id] = $catcolumn[cat_id]";
    $result =& $dbconn->Execute($sql);
    list($title, $cattitle) = $result->fields;

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$title:$lid", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_modrequest_column'];
    $sql = "DELETE FROM $pntable[links_modrequest]
            WHERE $column[requestid]='".(int)DataUtil::formatForStore($requestid)."'";
    $dbconn->Execute($sql);

    // the link has been ignored
    LogUtil::registerStatus (_WL_IGNORELINK);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'listmodrequests'));
}


function Web_Links_admin_modlink()
{
    // Get parameters from whatever input we need
    $lid = (int)FormUtil::getPassedValue('lid', isset($args['lid']) ? $args['lid'] : null, 'GETPOST');

    $link = pnModAPIFunc('Web_Links', 'admin', 'getmodlink', array('lid' => $lid));

    if (!$link) {
        return pnVarPrepHTMLDisplay(_WL_NOEXISTINGLINK);
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    $pnRender->assign('link', $link);
    $pnRender->assign('authid', pnSecGenAuthKey());

    return $pnRender->fetch('weblinks_admin_modlink.html');
}

function Web_Links_admin_modlinks()
{
    // Get parameters from whatever input we need
    $link = FormUtil::getPassedValue('link', isset($args['link']) ? $args['link'] : null, 'POST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $linkcolumn = &$pntable['links_links_column'];
    $linktable = $pntable['links_links'];
    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $linkcolumn[title], $catcolumn[title]
            FROM $linktable, $cattable
            WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($link['lid'])."'
            AND $linkcolumn[cat_id] = $catcolumn[cat_id]";
    $result =& $dbconn->Execute($sql);

    list($oldtitle, $cattitle) = $result->fields;

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$oldtitle:$lid", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_links_column'];
    $sql = "UPDATE $pntable[links_links]
            SET $column[cat_id]=".(int)DataUtil::formatForStore($link['cat']).",
                $column[title]='".DataUtil::formatForStore($link['title'])."',
                $column[url]='".DataUtil::formatForStore($link['url'])."',
                $column[description]='".DataUtil::formatForStore($link['description'])."',
                $column[name]='".DataUtil::formatForStore($link['name'])."',
                $column[email]='".DataUtil::formatForStore($link['email'])."',
                $column[hits]='".(int)DataUtil::formatForStore($link['hits'])."'
            WHERE $column[lid]='".(int)DataUtil::formatForStore($link['lid'])."'";
    $dbconn->Execute($sql);

    // Let any other modules know we have updated an item
    pnModCallHooks('item', 'update', $link['lid'], array('module' => 'Web_Links'));

    // the link has been modifyed successfuly
    LogUtil::registerStatus (_WL_MODIFYLINKSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
}

function Web_Links_admin_dellink()
{
    $lid = (int)FormUtil::getPassedValue('lid', isset($args['lid']) ? $args['lid'] : null, 'GET');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $linkcolumn = &$pntable['links_links_column'];
    $linktable = $pntable['links_links'];
    $catcolumn = &$pntable['links_categories_column'];
    $cattable = $pntable['links_categories'];
    $sql = "SELECT $linkcolumn[title], $catcolumn[title]
            FROM $linktable, $cattable
            WHERE $linkcolumn[lid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $linkcolumn[cat_id] = $catcolumn[cat_id]";
    $result =& $dbconn->Execute($sql);

    list($oldtitle, $cattitle) = $result->fields;

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Link', "$cattitle:$oldtitle:$lid", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    $column = &$pntable['links_links_column'];
    $sql = "DELETE FROM $pntable[links_links]
            WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    $dbconn->Execute($sql);

    // Let any hooks know that we have deleted an item.
    pnModCallHooks('item', 'delete', $lid, array('module' => 'Web_Links'));

    // the link has been deleted successfuly
    LogUtil::registerStatus (_WL_DELLINKSUCCESSFULY);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'linkview'));
}

function Web_Links_admin_getconfig() //fertig
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links', false);

    // assign the module vars
    $pnRender->assign('config', pnModGetVar('Web_Links'));

    // Return the output that has been generated by this function
    return $pnRender->fetch('weblinks_admin_getconfig.html');
}

function Web_Links_admin_updateconfig() //fertig
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // get our input
    $config = FormUtil::getPassedValue('config', 'array()', 'POST');

    // Confirm authorisation code.
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }

    // Update module variables
    if ( !isset($config['perpage']) || !is_numeric($config['perpage']) ) {
        $config['perpage'] = 10;
    }
    pnModSetVar('Web_Links', 'perpage', $config['perpage']);

    if ( !isset($config['toplinkspercentrigger']) || !is_numeric($config['toplinkspercentrigger']) ) {
        $config['toplinkspercentrigger'] = 0;
    }
    pnModSetVar('Web_Links', 'toplinkspercentrigger', $config['toplinkspercentrigger']);

    if ( !isset($config['toplinks']) || !is_numeric($config['toplinks']) ) {
        $config['toplinks'] = 25;
    }
    pnModSetVar('Web_Links', 'toplinks', $config['toplinks']);

    if ( !isset($config['mostpoplinkspercentrigger']) || !is_numeric($config['mostpoplinkspercentrigger']) ) {
        $config['mostpoplinkspercentrigger'] = 0;
    }
    pnModSetVar('Web_Links', 'mostpoplinkspercentrigger', $config['mostpoplinkspercentrigger']);

    if ( !isset($config['mostpoplinks']) || !is_numeric($config['mostpoplinks']) ) {
        $config['mostpoplinks'] = 25;
    }
    pnModSetVar('Web_Links', 'mostpoplinks', $config['mostpoplinks']);

    if ( !isset($config['featurebox']) || !is_numeric($config['featurebox']) ) {
        $config['featurebox'] = 1;
    }
    pnModSetVar('Web_Links', 'featurebox', $config['featurebox']);

    if ( !isset($config['targetblank']) || !is_numeric($config['targetblank']) ) {
        $config['targetblank'] = 0;
    }
    pnModSetVar('Web_Links', 'targetblank', $config['targetblank']);

    if ( !isset($config['blockunregmodify']) || !is_numeric($config['blockunregmodify']) ) {
        $config['blockunregmodify'] = 0;
    }
    pnModSetVar('Web_Links', 'blockunregmodify', $config['blockunregmodify']);

    if ( !isset($config['popular']) || !is_numeric($config['popular']) ) {
        $config['popular'] = 500;
    }
    pnModSetVar('Web_Links', 'popular', $config['popular']);

    if ( !isset($config['newlinks']) || !is_numeric($config['newlinks']) ) {
        $config['newlinks'] = 10;
    }
    pnModSetVar('Web_Links', 'newlinks', $config['newlinks']);

    if ( !isset($config['bestlinks']) || !is_numeric($config['bestlinks']) ) {
        $config['bestlinks'] = 10;
    }
    pnModSetVar('Web_Links', 'bestlinks', $config['bestlinks']);

    if ( !isset($config['linksresults']) || !is_numeric($config['linksresults']) ) {
        $config['linksresults'] = 10;
    }
    pnModSetVar('Web_Links', 'linksresults', $config['linksresults']);

    if ( !isset($config['links_anonaddlinklock']) || !is_numeric($config['links_anonaddlinklock']) ) {
        $config['links_anonaddlinklock'] = 1;
    }
    pnModSetVar('Web_Links', 'links_anonaddlinklock', $config['links_anonaddlinklock']);

    // the module configuration has been updated successfuly
    LogUtil::registerStatus (_WL_CONFIGUPDATED);

    return pnRedirect(pnModURL('Web_Links', 'admin', 'getconfig'));
}