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
 * get available admin panel links
 */
function Weblinks_adminapi_getlinks() // ready
{
    pnModLangLoad('Weblinks', 'admin');

    $links = array();
    
    if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_EDIT)) {
        $links[] = array('url' => pnModURL('Weblinks', 'admin', 'view'), 'text' => _WL_OVERVIEW);
    }
    if (SecurityUtil::checkPermission('Weblinks::Category', '::', ACCESS_EDIT)) {
        $links[] = array('url' => pnModURL('Weblinks', 'admin', 'catview'), 'text' => _WL_CATVIEW);
    }
    if (SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_EDIT)) {
        $links[] = array('url' => pnModURL('Weblinks', 'admin', 'linkview'), 'text' => _WL_LINKVIEW);
    }
    if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('Weblinks', 'admin', 'getconfig'), 'text' => _WL_MODCONF);
        $links[] = array('url' => pnModURL('Weblinks', 'admin', 'import'), 'text' => _WL_IMPORT);
    }
    if (SecurityUtil::checkPermission('Weblinks::Link', '::', ACCESS_EDIT)) {
        $links[] = array('url' => pnModURL('Weblinks', 'user', 'view'), 'text' => _WL_INDEX);
    }
    return $links;
}

/**
 * count brocken links
  */
function Weblinks_adminapi_countbrokenlinks() // ready
{
    return DBUtil::selectObjectCountByID('links_modrequest', 1, 'brokenlink');
}

/**
 * count modrequests for links
 */
function Weblinks_adminapi_countmodrequests() // ready
{
    $pntable =& pnDBGetTables();
    $column = &$pntable['links_modrequest_column'];
    $where = "WHERE $column[brokenlink]='0'";
    return DBUtil::selectObjectCount('links_modrequest', $where);
}

/**
 * get new links waiting for approve
 */
function Weblinks_adminapi_newlinks() // ready
{
    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_DELETE);

    // get newlinks vars from db
    $newlinks = DBUtil::selectObjectArray('links_newlink', '', 'lid', '-1', '-1', '', $permFilter);

    // check for a db error
    if ($newlinks === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return the newlinks array
    return $newlinks;
}

/**
 * add a category to db
 */
function Weblinks_adminapi_addcategory($args) // ready
{
    // Argument check
    if (!isset($args['pid']) || !is_numeric($args['pid']) || !isset($args['title'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    $checkcat = DBUtil::selectObjectCountByID('links_categories', $args['pid'], 'parent_id', 'lower');
    $checktitle = DBUtil::selectObjectCountByID('links_categories', $args['title'], 'title', 'lower');
    if ($checkcat && $checktitle) {
        return LogUtil::registerError(_WL_ERRORTHECATEGORY);
    }

    $items = array('parent_id' => $args['pid'], 'title' => $args['title'], 'cdescription' => $args['cdescription']);
    if (!DBUtil::insertObject($items, 'links_categories', 'cat_id')) {
        return LogUtil::registerError(_WL_ERRORTHECATEGORY);
    }
      
    return true;
}

/**
 * get vars from category to modify
 */
function Weblinks_adminapi_getcategory($args) // ready
{
    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_EDIT);

    // get the category vars from the db
    $category = DBUtil::selectObjectById('links_categories', $args['cid'], 'cat_id', '', $permFilter);

    // check for a db error
    if ($category === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return the category array
    return $category;
}

/**
 * update category vars
 */
function Weblinks_adminapi_updatecategory($args) // ready
{
    // Argument check
    if (!isset($args['cid']) || !is_numeric($args['cid']) || !isset($args['title'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $pntable =& pnDBGetTables();
    $column = &$pntable['links_categories_column'];
    $items = array('title' => $args['title'], 'cdescription' => $args['cdescription']);
    $where = "WHERE $column[cat_id]='".(int)DataUtil::formatForStore($args['cid'])."'";
    if (!DBUtil::updateObject($items, 'links_categories', $where, 'cat_id')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    return true;
}

/**
 * delete a category
 */
function Weblinks_adminapi_delcategory($args) // ready
{
    // Argument check
    if (!isset($args['cid']) || !is_numeric($args['cid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Category', "::", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    $pntable =& pnDBGetTables();

    // delete links
    $linkcolumn = &$pntable['links_links_column'];
    $where = "WHERE $linkcolumn[cat_id] = '".(int)DataUtil::formatForStore($args['cid'])."'";
    if (!DBUtil::deleteWhere('links_links', $where)) {
        return false;
    }

    // delete subcategories
    $catcolumn = &$pntable['links_categories_column'];
    $where = "WHERE '".(int)DataUtil::formatForStore($args['cid'])."' = $catcolumn[parent_id]";
    if (!DBUtil::deleteWhere('links_categories', $where)) {
        return false;
    }
    
    // delete category 
    $where = "WHERE $catcolumn[cat_id] = '".(int)DataUtil::formatForStore($args['cid'])."'";
    if (!DBUtil::deleteWhere('links_categories', $where)) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    return true;
}

/**
 * add a link to db
 */
function Weblinks_adminapi_addlink($args) // ready
{
    // Argument check
    if (!isset($args['cid']) || !isset($args['title']) || !isset($args['url']) || !isset($args['date'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    $items = array('cat_id' => $args['cid'], 'title' => $args['title'],'url' => $args['url'], 'description' => $args['description'], 'date' => $args['date'], 'name' => $args['name'], 'email' => $args['email'], 'submitter' => $args['submitter']);
    if (!DBUtil::insertObject($items, 'links_links', 'lid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    // get lid from the new link
    $lid = DBUtil::getInsertID('links_links', 'lid');
    
    // Let any hooks know that we have created a new link.
    pnModCallHooks('item', 'display', $lid, array('module' => 'Weblinks'));

    return true;
}

/**
 * delete a newlink
 */
function Weblinks_adminapi_delnewlink($args) // ready
{
    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError ();
    }
    
    // delete link from the newlink table
    if (!DBUtil::deleteObjectByID('links_newlink', $args['lid'], 'lid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    return true;
}

/**
 * get link vars for modify
 */
function Weblinks_adminapi_getlink($args) //ready
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
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Link',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'lid',
                          'level'            => ACCESS_EDIT);    

    // get the object from the db
    $link = DBUtil::selectObjectById('links_links', $args['lid'], 'lid', '', $permFilter);

    // check for a db error
    if ($link === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $link;
}

/**
 * update link vars
 */
function Weblinks_adminapi_updatelink($args) // ready
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

    $pntable =& pnDBGetTables();
    $column = &$pntable['links_links_column'];
    $items = array('cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description'], 'name' => $args['name'], 'email' => $args['email'], 'hits' => $args['hits']);
    $where = "WHERE $column[lid]='".(int)DataUtil::formatForStore($args['lid'])."'";
    if (!DBUtil::updateObject($items, 'links_links', $where, 'lid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    // Let any other modules know we have updated an item
    pnModCallHooks('item', 'update', $args['lid'], array('module' => 'Weblinks'));
    
    return true;
}

/**
 * delete a link
 */
function Weblinks_adminapi_dellink($args) // ready
{
    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError ();
    }

    if (!DBUtil::deleteObjectByID('links_links', $args['lid'], 'lid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    // Let any hooks know that we have deleted a link.
    pnModCallHooks('item', 'delete', $args['lid'], array('module' => 'Weblinks'));
    
    return true;
}

/**
 * check links
 */
function Weblinks_adminapi_checklinks($args) // ready
{
    // Argument check
    if (!isset($args['cid']) || !is_numeric($args['cid'])) {
        return LogUtil::registerError (_MODARGSERROR);
    }
    
    // Check ALL Links
    if ($args['cid'] == 0) {
        $where = "";
    }
    
    // Check Categories
    if ($args['cid'] != 0) {
        $pntable =& pnDBGetTables();
        $column = &$pntable['links_links_column'];
        $where = "WHERE $column[cat_id]='".(int)DataUtil::formatForStore($args['cid'])."'";
    }

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_EDIT);

    // get the vars from the db
    $checkcatlinks = DBUtil::selectObjectArray('links_links', $where, 'title', '-1', '-1', '', $permFilter);

    // check for a db error
    if ($checkcatlinks === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // put items into result array.
    $links = array();
    foreach ($checkcatlinks as $link) {
        if ($link['url'] == 'http://' || $link['url'] == '') {
            $fp = false;
        } else {
            $vurl = parse_url($link['url']);
            $fp = fsockopen($vurl['host'], 80, $errno, $errstr, 15);
        }

        $links[] = array('lid'   => $link['lid'],
                         'title' => $link['title'],
                         'url'   => $link['url'],
                         'fp'    => $fp);
    }
  
    // return array
    return $links;
}    

/**
 * get broken links
 */
function Weblinks_adminapi_brokenlinks() // ready
{
    $pntable =& pnDBGetTables();
    $column = &$pntable['links_modrequest_column'];
    $where = "WHERE $column[brokenlink]='1'";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_EDIT);

    // get the vars from the db
    $objArray = DBUtil::selectObjectArray('links_modrequest', $where, 'requestid', '-1', '-1', '', $permFilter);

    // check for a db error
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // put items into result array.
    $brokenlinks = array();
    foreach ($objArray as $request) {
        $link = pnModAPIFunc('Weblinks', 'admin', 'getlink', array('lid' => $request['lid']));
        
        if ($request['modifysubmitter'] != pnConfigGetVar('anonymous')) {
            $email = DBUtil::selectObjectByID('users', $request['modifysubmitter'], 'uname');
        }

        $brokenlinks[] = array('lid'            => $request['lid'],
                               'rid'            => $request['requestid'],
                               'title'          => $link['title'],
                               'url'            => $link['url'],
                               'submitter'      => $request['modifysubmitter'],
                               'submitteremail' => $email['email'],
                               'owner'          => $link['name'],
                               'owneremail'     => $link['email']);
    }
  
    // return array
    return $brokenlinks;
}

/**
 * delete a request
 */
function Weblinks_adminapi_delrequest($args) // ready
{
    // Argument check
    if (!isset($args['rid']) || !is_numeric($args['rid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_DELETE)) {
        return LogUtil::registerPermissionError ();
    }
    
    if (!DBUtil::deleteObjectByID('links_modrequest', $args['rid'], 'requestid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    return true;
}

/**
 * get links with modrequest
 */
function Weblinks_adminapi_modrequests() // ready
{
    $pntable =& pnDBGetTables();
    $column = &$pntable['links_modrequest_column'];
    $where = "WHERE $column[brokenlink]='0'";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_EDIT);

    // get the vars from the db
    $objArray = DBUtil::selectObjectArray('links_modrequest', $where, 'requestid', '-1', '-1', '', $permFilter);

    // check for a db error
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // put items into result array.
    $modrequests = array();
    foreach ($objArray as $request) {
        $link = pnModAPIFunc('Weblinks', 'admin', 'getlink', array('lid' => $request['lid']));
        $category = pnModAPIFunc('Weblinks', 'admin', 'getcategory', array('cid' => $request['cat_id']));
        $origcategory = pnModAPIFunc('Weblinks', 'admin', 'getcategory', array('cid' => $link['cat_id']));
        
        if ($request['modifysubmitter'] != pnConfigGetVar('anonymous')) {
            $email = DBUtil::selectObjectByID('users', $request['modifysubmitter'], 'uname');
        }

        $modrequests[] = array('rid'             => $request['requestid'],
                               'lid'             => $request['lid'],
                               'title'           => $request['title'],
                               'url'             => $request['url'],
                               'description'     => $request['description'],
                               'cid'             => $request['cat_id'],
                               'cidtitle'        => $category['title'],
                               'origtitle'       => $link['title'],
                               'origurl'         => $link['url'],
                               'origdescription' => $link['description'],
                               'origcid'         => $link['cat_id'],
                               'origcidtitle'    => $origcategory['title'],
                               'submitter'       => $request['modifysubmitter'],
                               'submitteremail'  => $email['email'],
                               'owner'           => $link['name'],
                               'owneremail'      => $link['email']);
    }
  
    // return array
    return $modrequests;
}

/**
 * get link with modrequest
 */
function Weblinks_adminapi_linkmodrequest($args) // ready
{
    // Argument check
    if ((!isset($args['rid']) || !is_numeric($args['rid']))) {
        return LogUtil::registerArgsError();
    }
    
    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Link',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'lid',
                          'level'            => ACCESS_EDIT);   

    // get the object from the db
    $requestlink = DBUtil::selectObjectById('links_modrequest', $args['rid'], 'requestid', '', $permFilter);

    // check for a db error
    if ($requestlink === false) {
        return LogUtil::registerError (_GETFAILED);
    }    
 
    return $requestlink;
}

/**
 * update link vars from modrequest
 */
function Weblinks_adminapi_updatemodlink($args) // ready
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

    $pntable =& pnDBGetTables();
    $column = &$pntable['links_links_column'];
    $items = array('cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description']);
    $where = "WHERE $column[lid]='".(int)DataUtil::formatForStore($args['lid'])."'";
    if (!DBUtil::updateObject($items, 'links_links', $where, 'lid')) {
        return LogUtil::registerError(_GETFAILED);
    }
    
    // Let any other modules know we have updated an item
    pnModCallHooks('item', 'update', $args['lid'], array('module' => 'Weblinks'));
    
    return true;
}