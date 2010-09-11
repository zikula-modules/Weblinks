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
 * get all categories
 */
function Weblinks_userapi_categories() // ready
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
                          'level'            => ACCESS_READ);

    // get all categories from db
    $categories = DBUtil::selectObjectArray('weblinks_categories', '', 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($categories === false) {
        return LogUtil::registerError(_GETFAILED);
    }

    // return the categories array
    return $categories;
}

/**
 * count all links
 */
function Weblinks_userapi_numrows() // ready
{
    // count links in db
    return DBUtil::selectObjectCount('weblinks_links');
}

/**
 * count all categories
 */
function Weblinks_userapi_catnum() // ready
{
    // count categories in db
    return DBUtil::selectObjectCount('weblinks_categories');
}

/**
 * get a specific category
 */
function Weblinks_userapi_category($args) // ready
{
    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerArgsError();
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
                          'level'            => ACCESS_READ);

    // get the category vars from the db
    $category = DBUtil::selectObjectById('weblinks_categories', $args['cid'], 'cat_id', '', $permFilter);

    // check for db error
    if ($category === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return the category array
    return $category;
}

/**
 * get subcategories
 */
function Weblinks_userapi_subcategory($args) // ready
{
    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerArgsError();
    }

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_categories_column'];

    $where = "WHERE $weblinkscolumn[parent_id] = ".(int)DataUtil::formatForStore($args['cid']);

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get the subcategories vars from the db
    $subcategories = DBUtil::selectObjectArray('weblinks_categories', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($subcategories === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return the subcategories array
    return $subcategories;
}

/**
 * get weblinks
 */
function Weblinks_userapi_weblinks($args) // ready
{
    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerArgsError();
    }

    $orderbysql = (isset($args['orderbysql'])) ? $args['orderbysql'] : 'titleA';
    $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
    $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_links_column'];

    $where = "WHERE $weblinkscolumn[cat_id] = ".(int)DataUtil::formatForStore($args['cid']);
    
    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get the weblinks vars from the db
    $weblinks = DBUtil::selectObjectArray('weblinks_links', $where, $orderbysql, $startnum-1, $numlinks, '', $permFilter);

    // chack for db error
    if ($weblinks === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return the weblinks array
    return $weblinks;
}

/**
 * order funktion
 */
function Weblinks_userapi_orderby($args) // ready
{
    $pntable =& pnDBGetTables();
    $column = &$pntable['weblinks_links_column'];

    if ($args['orderby'] == "titleA") {
        $orderbysql = "$column[title] ASC";
    } else if ($args['orderby'] == "dateA") {
        $orderbysql = "$column[date] ASC";
    } else if ($args['orderby'] == "hitsA") {
        $orderbysql = "$column[hits] ASC";
    } else if ($args['orderby'] == "titleD") {
        $orderbysql = "$column[title] DESC";
    } else if ($args['orderby'] == "dateD") {
        $orderbysql = "$column[date] DESC";
    } else if ($args['orderby'] == "hitsD") {
        $orderbysql = "$column[hits] DESC";
    } else {
        $orderbysql = "$column[title] ASC";
    }
    return $orderbysql;
}

/**
 * count weblinks in the category
 */
function Weblinks_userapi_countcatlinks($args) // ready
{
    return DBUtil::selectObjectCountByID('weblinks_links', $args['cid'], 'cat_id');
}

/**
 * get weblink array
 */
function Weblinks_userapi_link($args) // ready
{
    // Argument check
    if ((!isset($args['lid']) || !is_numeric($args['lid']))) {
        return LogUtil::registerArgsError();
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
                          'level'            => ACCESS_READ);

    // get link array 
    $link = DBUtil::selectObjectByID('weblinks_links', $args['lid'], 'lid', '', $permFilter);

    // check for db error
    if ($link === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    // return link array
    return $link;
}

/**
 * update hits for a link
 */
function Weblinks_userapi_hitcountinc($args) // ready
{
    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid']) ||
        !isset($args['hits']) || !is_numeric($args['hits'])) {
        return LogUtil::registerArgsError();
    }

    $hits = $args['hits'] + 1;

    $pntable =& pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_links_column'];

    $items = array('hits' => $hits);
    $where = "WHERE $weblinkscolumn[lid] = ".DataUtil::formatForStore($args['lid']);

    return DBUtil::updateObject($items, 'weblinks_links', $where, 'lid');
}

/**
 * get categories with query inside
 */
function Weblinks_userapi_searchcats($args) // ready
{
    // Argument check
    if (!isset($args['query'])) {
        return LogUtil::registerArgsError();
    }

    $pntable =& pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_categories_column'];

    $where ="WHERE $weblinkscolumn[title] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get categories from db
    $searchcats = DBUtil::selectObjectArray('weblinks_categories', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($searchcats === false) {
        return LogUtil::registerError (_GETFAILED);
    }
    
    // Return the subcategories array
    return $searchcats;
}

/**
 * get weblinks with query inside
 */
function Weblinks_userapi_search_weblinks($args) // ready
{
    // Argument check
    if (!isset($args['query'])) {
        return LogUtil::registerArgsError();
    }

    $orderby = (isset($args['orderby'])) ? $args['orderby'] : 'titleA';
    $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
    $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

    $pntable =& pnDBGetTables();
    $column = &$pntable['weblinks_links_column'];

    $where = "$column[title] LIKE '%".DataUtil::formatForStore($args['query'])."%' OR $column[description] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    $result = DBUtil::selectObjectArray('weblinks_links', $where, $args['orderby'], $args['startnum']-1, $args['numlinks'], '', $permFilter);

    // check for db error
    if ($result === false) {
        return LogUtil::registerError (_GETFAILED);
    }
    
    // Return the array
    return $result;
}

/**
 * count searchlinks
 */
function Weblinks_userapi_countsearchlinks($args) // ready
{
    // Argument check
    if (!isset($args['query'])) {
        return LogUtil::registerArgsError();
    }

    $pntable =& pnDBGetTables();
    $column = &$pntable['weblinks_links_column'];

    $where = "$column[title] LIKE '%".DataUtil::formatForStore($args['query'])."%' OR $column[description] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

    return DBUtil::selectObjectCount('links_links', $where);
}
/**
 * get a random link
 */
function Weblinks_userapi_random() // ready
{
    $numrows = pnModAPIFunc('Weblinks', 'user', 'numrows');

    if ($numrows < 1 ) { // if no data
        return pnVarPrepHTMLDisplay(_WEBLINKS_NOLINKS);
    }
    if ($numrows == 1) {
        $lid = 1;
    } else {
        srand((double)microtime()*1000000);
        $lid = rand(1,$numrows);
    }

    return $lid;
}

/**
 * get number of links per day
 */
function Weblinks_userapi_totallinks($args) // ready
{
    // Argument check
    if (!isset($args['selectdate']) || !is_numeric($args['selectdate'])) {
        return LogUtil::registerArgsError();
    }

    $newlinkdb = date("Y-m-d", $args['selectdate']);

    $pntable =& pnDBGetTables();
    $column = &$pntable['weblinks_links_column'];
    $column2 = &$pntable['weblinks_categories_column'];

    $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";

    return DBUtil::selectObjectCount('weblinks_links', $where);
}

/**
 * get weblinks by day
 */
function Weblinks_userapi_weblinksbydate($args) // ready
{
    if (!isset($args['selectdate']) || !is_numeric($args['selectdate'])) {
        return LogUtil::registerArgsError();
    }

    $pntable =& pnDBGetTables();
    $newlinkdb = date("Y-m-d", $args['selectdate']);
    $column = &$pntable['weblinks_links_column'];
    $where = "WHERE $column[date] LIKE '%".DataUtil::formatForStore($newlinkdb)."%'";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get weblinks from db
    $weblinks = DBUtil::selectObjectArray('weblinks_links', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($weblinks === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $weblinks;
}

/**
 * add broken link to db
 */
function Weblinks_userapi_addbrockenlink($args) // ready
{
    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    $items = array('lid' => $args['lid'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 1);
    if (!DBUtil::insertObject($items, 'weblinks_modrequest', 'requestid')) {
        return LogUtil::registerError(_GETFAILED);
    }

    return true;
}

/**
 * add link rewuest to db
 */
function Weblinks_userapi_modifylinkrequest($args) // ready
{
    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    $items = array('lid' => $args['lid'], 'cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 0);
    if (!DBUtil::insertObject($items, 'weblinks_modrequest', 'requestid')) {
        return LogUtil::registerError(_GETFAILED);
    }

    return true;
}

/**
 * add link to db
 */
function Weblinks_userapi_add($args) // ready
{
    // Security check
    if (!SecurityUtil::checkPermission('Weblinks::Link', "::", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    $checkurl = pnModAPIFunc('Weblinks', 'user', 'checkurl', array('url' => $args['url']));
    $valid = pnVarValidate($args['url'], 'url');

    if ($checkurl > 0) {
        $link['text'] = _WL_LINKALREADYEXT;
        $link['submit'] = 0;
        return $link;
    } else if ($valid == false) {
        $link['text'] = _WL_LINKNOURL;
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['title'])) {
        $link['text'] = _WL_LINKNOTITLE;
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['cid']) || !is_numeric($args['cid'])) {
        $link['text'] =_WL_LINKNOCAT;
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['description'])) {
        $link['text'] =_WL_LINKNODESC;
        $link['submit'] = 0;
        return $link;
    } else {
        if (empty($args['submitter'])) {
            $link['submitter'] = pnConfigGetVar("anonymous");
        }
        
        $items = array('cat_id' => $args['cid'], 'title' => $args['title'],'url' => $args['url'], 'description' => $args['description'], 'name' => $args['submitter'], 'email' => $args['submitteremail'], 'submitter' => $args['submitter']);
        if (!DBUtil::insertObject($items, 'weblinks_newlink', 'lid')) {
            return LogUtil::registerError(_GETFAILED);
        }
    
        if (empty($args['submitteremail'])) {
            $link['text'] = _WL_CHECKFORIT;
        } else {
            $link['text'] = _WL_EMAILWHENADD;
        }
        $link['submit'] = 1;
        return $link;
    }
}

/**
 * check if url exists
 */
function Weblinks_userapi_checkurl($args) // ready
{
    return DBUtil::selectObjectCountByID('weblinks_links', $args['url'], 'url', 'lower');
}

/**
 * get the last weblinks
 */
function Weblinks_userapi_lastweblinks($args) // ready
{
    // Argument check
    if (!isset($args['lastlinks']) || !is_numeric($args['lastlinks'])) {
        $args['lastlinks'] = pnModGetVar('Weblinks', 'linksinblock');
    }

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_links_column'];

    $orderby = "ORDER BY $weblinkscolumn[date] DESC";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get last weblinks from db
    $lastweblinks = DBUtil::selectObjectArray('weblinks_links', '', $orderby, '-1', $args['lastlinks'], '', $permFilter);

    // check for db error
    if ($lasweblinks === false) {
        return LogUtil::registerError(_GETFAILED);
    }

    // return the last weblinks
    return $lastweblinks;
}

/**
 * get the most popular weblinks
 */
function Weblinks_userapi_mostpopularweblinks($args) // ready
{
    // Argument check
    if (!isset($args['mostpoplinks']) || !is_numeric($args['mostpoplinks'])) {
        $args['mostpoplinks'] = pnModGetVar('Weblinks', 'linksinblock');
    }
    
    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['weblinks_links_column'];

    $orderby = "ORDER BY $weblinkscolumn[hits] DESC";

    // define the permission filter to apply
    $permFilter = array();
    $permFilter[] = array('realm'            => 0,
                          'component_left'   => 'Weblinks',
                          'component_middle' => '',
                          'component_right'  => 'Category',
                          'instance_left'    => 'title',
                          'instance_middle'  => '',
                          'instance_right'   => 'cat_id',
                          'level'            => ACCESS_READ);

    // get most popular weblinks from db
    $mostpopularweblinks = DBUtil::selectObjectArray('weblinks_links', '', $orderby, '-1', $args['mostpoplinks'], '', $permFilter);

    // check for db error
    if ($mostpopularweblinks === false) {
        return LogUtil::registerError(_GETFAILED);
    }

    // return the most popular weblinks
    return $mostpopularweblinks;
}