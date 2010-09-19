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
    $dom = ZLanguage::getModuleDomain('Weblinks');

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
    $categories = DBUtil::selectObjectArray('links_categories', '', 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($categories === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
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
    return DBUtil::selectObjectCount('links_links');
}

/**
 * count all categories
 */
function Weblinks_userapi_catnum() // ready
{
    // count categories in db
    return DBUtil::selectObjectCount('links_categories');
}

/**
 * get a specific category
 */
function Weblinks_userapi_category($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

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
    $category = DBUtil::selectObjectById('links_categories', $args['cid'], 'cat_id', '', $permFilter);

    // check for db error
    if ($category === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // return the category array
    return $category;
}

/**
 * get subcategories
 */
function Weblinks_userapi_subcategory($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerArgsError();
    }

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_categories_column'];

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
    $subcategories = DBUtil::selectObjectArray('links_categories', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($subcategories === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // return the subcategories array
    return $subcategories;
}

/**
 * get weblinks
 */
function Weblinks_userapi_weblinks($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerArgsError();
    }

    $orderbysql = (isset($args['orderbysql'])) ? $args['orderbysql'] : 'titleA';
    $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
    $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_links_column'];

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
    $weblinks = DBUtil::selectObjectArray('links_links', $where, $orderbysql, $startnum-1, $numlinks, '', $permFilter);

    // chack for db error
    if ($weblinks === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
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
    $column = &$pntable['links_links_column'];

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
    return DBUtil::selectObjectCountByID('links_links', $args['cid'], 'cat_id');
}

/**
 * get weblink array
 */
function Weblinks_userapi_link($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

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
    $link = DBUtil::selectObjectByID('links_links', $args['lid'], 'lid', '', $permFilter);

    // check for db error
    if ($link === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
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
    $weblinkscolumn = &$pntable['links_links_column'];

    $items = array('hits' => $hits);
    $where = "WHERE $weblinkscolumn[lid] = ".DataUtil::formatForStore($args['lid']);

    return DBUtil::updateObject($items, 'links_links', $where, 'lid');
}

/**
 * get categories with query inside
 */
function Weblinks_userapi_searchcats($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['query'])) {
        return LogUtil::registerArgsError();
    }

    $pntable =& pnDBGetTables();
    $weblinkscolumn = &$pntable['links_categories_column'];

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
    $searchcats = DBUtil::selectObjectArray('links_categories', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($searchcats === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // Return the subcategories array
    return $searchcats;
}

/**
 * get weblinks with query inside
 */
function Weblinks_userapi_search_weblinks($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['query'])) {
        return LogUtil::registerArgsError();
    }

    $orderby = (isset($args['orderby'])) ? $args['orderby'] : 'titleA';
    $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
    $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

    $pntable =& pnDBGetTables();
    $column = &$pntable['links_links_column'];

    $where = "WHERE $column[title] LIKE '%".DataUtil::formatForStore($args['query'])."%' OR $column[description] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

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

    $result = DBUtil::selectObjectArray('links_links', $where, $args['orderby'], $args['startnum']-1, $args['numlinks'], '', $permFilter);

    // check for db error
    if ($result === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
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
    $column = &$pntable['links_links_column'];

    $where = "WHERE $column[title] LIKE '%".DataUtil::formatForStore($args['query'])."%' OR $column[description] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

    return DBUtil::selectObjectCount('links_links', $where);
}
/**
 * get a random link
 */
function Weblinks_userapi_random() // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    $numrows = pnModAPIFunc('Weblinks', 'user', 'numrows');

    if ($numrows < 1 ) { // if no data
        return pnVarPrepHTMLDisplay(__('Sorry! There is no such link', $dom));
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
    $column = &$pntable['links_links_column'];
    $column2 = &$pntable['links_categories_column'];

    $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";

    return DBUtil::selectObjectCount('links_links', $where);
}

/**
 * get weblinks by day
 */
function Weblinks_userapi_weblinksbydate($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    if (!isset($args['selectdate']) || !is_numeric($args['selectdate'])) {
        return LogUtil::registerArgsError();
    }

    $pntable =& pnDBGetTables();
    $newlinkdb = date("Y-m-d", $args['selectdate']);
    $column = &$pntable['links_links_column'];
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
    $weblinks = DBUtil::selectObjectArray('links_links', $where, 'title', '-1', '-1', '', $permFilter);

    // check for db error
    if ($weblinks === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    return $weblinks;
}

/**
 * add broken link to db
 */
function Weblinks_userapi_addbrockenlink($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!pnModGetVar('Weblinks', 'unregbroken') == 1 &&
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    $items = array('lid' => $args['lid'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 1);
    if (!DBUtil::insertObject($items, 'links_modrequest', 'requestid')) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    return true;
}

/**
 * add link rewuest to db
 */
function Weblinks_userapi_modifylinkrequest($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['lid']) || !is_numeric($args['lid'])) {
        return LogUtil::registerArgsError();
    }

    // Security check
    if (!pnModGetVar('Weblinks', 'blockunregmodify') == 1 &&
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    $items = array('lid' => $args['lid'], 'cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 0);
    if (!DBUtil::insertObject($items, 'links_modrequest', 'requestid')) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    return true;
}

/**
 * add link to db
 */
function Weblinks_userapi_add($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Security check
    if (!pnModGetVar('Weblinks', 'links_anonaddlinklock') == 1 &&
        !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

    $checkurl = pnModAPIFunc('Weblinks', 'user', 'checkurl', array('url' => $args['url']));
    $valid = pnVarValidate($args['url'], 'url');

    if ($checkurl > 0) {
        $link['text'] = __('Sorry! This URL is already listed in the database!', $dom);
        $link['submit'] = 0;
        return $link;
    } else if ($valid == false) {
        $link['text'] = __('Sorry! Error! You must type a URL for the web link!', $dom);
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['title'])) {
        $link['text'] = __('Sorry! Error! You must type a title for the URL!', $dom);
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['cid']) || !is_numeric($args['cid'])) {
        $link['text'] =__('Sorry! Error! No category!', $dom);
        $link['submit'] = 0;
        return $link;
    } else if (empty($args['description'])) {
        $link['text'] =__('Sorry! Error! You must type a description for the URL!', $dom);
        $link['submit'] = 0;
        return $link;
    } else {
        if (empty($args['submitter'])) {
            $link['submitter'] = pnConfigGetVar("anonymous");
        }

        $items = array('cat_id' => $args['cid'], 'title' => $args['title'],'url' => $args['url'], 'description' => $args['description'], 'name' => $args['submitter'], 'email' => $args['submitteremail'], 'submitter' => $args['submitter']);
        if (!DBUtil::insertObject($items, 'links_newlink', 'lid')) {
            return LogUtil::registerError(__('Error! Could not load items.', $dom));
        }

        if (empty($args['submitteremail'])) {
            $link['text'] = __('You didn\'t enter an e-mail address. However, your link will still be checked.', $dom);
        } else {
            $link['text'] = __('Thank you! You\'ll receive an e-mail message when it\'s approved.', $dom);
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
    return DBUtil::selectObjectCountByID('links_links', $args['url'], 'url', 'lower');
}

/**
 * get the last weblinks
 */
function Weblinks_userapi_lastweblinks($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['lastlinks']) || !is_numeric($args['lastlinks'])) {
        $args['lastlinks'] = pnModGetVar('Weblinks', 'linksinblock');
    }

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_links_column'];

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
    $lastweblinks = DBUtil::selectObjectArray('links_links', '', $orderby, '-1', $args['lastlinks'], '', $permFilter);

    // check for db error
    if ($lasweblinks === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // return the last weblinks
    return $lastweblinks;
}

/**
 * get the most popular weblinks
 */
function Weblinks_userapi_mostpopularweblinks($args) // ready
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Argument check
    if (!isset($args['mostpoplinks']) || !is_numeric($args['mostpoplinks'])) {
        $args['mostpoplinks'] = pnModGetVar('Weblinks', 'linksinblock');
    }

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_links_column'];

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
    $mostpopularweblinks = DBUtil::selectObjectArray('links_links', '', $orderby, '-1', $args['mostpoplinks'], '', $permFilter);

    // check for db error
    if ($mostpopularweblinks === false) {
        return LogUtil::registerError(__('Error! Could not load items.', $dom));
    }

    // return the most popular weblinks
    return $mostpopularweblinks;
}