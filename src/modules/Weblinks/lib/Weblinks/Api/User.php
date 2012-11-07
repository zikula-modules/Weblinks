<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
class Weblinks_Api_User extends Zikula_AbstractApi {

    /**
    * get all categories
    */
    public function categories()
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
        $categories = DBUtil::selectObjectArray('links_categories', '', 'title', '-1', '-1', '', $permFilter);

        // check for db error
        if ($categories === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the categories array
        return $categories;
    }

    /**
    * count all links
    */
    public function numrows()
    {
        // count links in db
        return DBUtil::selectObjectCount('links_links');
    }

    /**
    * count all categories
    */
    public function catnum()
    {
        // count categories in db
        return DBUtil::selectObjectCount('links_categories');
    }

    /**
    * get a specific category
    */
    public function category($args)
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
        $category = DBUtil::selectObjectById('links_categories', $args['cid'], 'cat_id', '', $permFilter);

        // check for db error
        if ($category === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the category array
        return $category;
    }

    /**
    * get subcategories
    */
    public function subcategory($args)
    {


        // Argument check
        if ((!isset($args['cid']) || !is_numeric($args['cid']))) {
            return LogUtil::registerArgsError();
        }

        $pntable = DBUtil::getTables();
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
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the subcategories array
        return $subcategories;
    }

    /**
    * get weblinks
    */
    public function weblinks($args)
    {


        $orderbysql = (isset($args['orderbysql'])) ? $args['orderbysql'] : 'titleA';
        $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
        $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

        // by rgasch to solve http://code.zikula.org/weblinks/ticket/37
        $where = "";
        if (isset($args['cid']) && is_numeric($args['cid']) && $args['cid']) {
            $pntable = DBUtil::getTables();
            $weblinkscolumn = &$pntable['links_links_column'];
            $where = "WHERE $weblinkscolumn[cat_id] = ".(int)DataUtil::formatForStore($args['cid']);
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

        // get the weblinks vars from the db
        $weblinks = DBUtil::selectObjectArray('links_links', $where, $orderbysql, $startnum-1, $numlinks, '', $permFilter);

        // chack for db error
        if ($weblinks === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the weblinks array
        return $weblinks;
    }

    /**
    * order funktion
    */
    public function orderby($args)
    {
        $pntable = DBUtil::getTables();
        $column = $pntable['links_links_column'];

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
    public function countcatlinks($args)
    {
        return DBUtil::selectObjectCountByID('links_links', $args['cid'], 'cat_id');
    }

    /**
    * get weblink array
    */
    public function link($args)
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
        $link = DBUtil::selectObjectByID('links_links', $args['lid'], 'lid', '', $permFilter);

        // check for db error
        if ($link === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return link array
        return $link;
    }

    /**
    * update hits for a link
    */
    public function hitcountinc($args)
    {
        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid']) ||
            !isset($args['hits']) || !is_numeric($args['hits'])) {
            return LogUtil::registerArgsError();
        }

        $hits = $args['hits'] + 1;

        $pntable = DBUtil::getTables();
        $weblinkscolumn = $pntable['links_links_column'];

        $items = array('hits' => $hits);
        $where = "WHERE $weblinkscolumn[lid] = ".DataUtil::formatForStore($args['lid']);

        return DBUtil::updateObject($items, 'links_links', $where, 'lid');
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

        $pntable = DBUtil::getTables();
        $weblinkscolumn = $pntable['links_categories_column'];

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
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

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

        $orderby = (isset($args['orderby'])) ? $args['orderby'] : 'titleA';
        $startnum = (isset($args['startnum']) && is_numeric($args['startnum'])) ? $args['startnum'] : 1;
        $numlinks = (isset($args['numlinks']) && is_numeric($args['numlinks'])) ? $args['numlinks'] : -1;

        $pntable = DBUtil::getTables();
        $column = $pntable['links_links_column'];

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
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

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

        $pntable = DBUtil::getTables();
        $column = $pntable['links_links_column'];

        $where = "WHERE $column[title] LIKE '%".DataUtil::formatForStore($args['query'])."%' OR $column[description] LIKE '%".DataUtil::formatForStore($args['query'])."%'";

        return DBUtil::selectObjectCount('links_links', $where);
    }
    /**
    * get random links
    */
    public function random($args)
    {


        $num = (isset($args['num']) && is_numeric($args['num'])) ? $args['num'] : 1;

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

        $objArray = DBUtil::selectObjectArray('links_links', '', '', '-1', '-1', '', $permFilter);
        foreach ($objArray as $link) {
            $lidarray[] = $link['lid'];
        }

        if ($lidarray < 1) { // if no link
            return DataUtil::formatForDisplayHTML($this->__('Sorry! There is no such link'));
        }

        $links = array_rand($lidarray, $num);

        if ($num > 1) {
            $lid = array();
            foreach ($links as $link) {
                $lid[] = array('lid' => $link, 'title' => DBUtil::selectFieldByID('links_links', 'title', $link, 'lid'));
            }
        } else {
            $lid = $links;
        }

        return $lid;
    }

    /**
    * get number of links per day
    */
    public function totallinks($args)
    {
        // Argument check
        if (!isset($args['selectdate']) || !is_numeric($args['selectdate'])) {
            return LogUtil::registerArgsError();
        }

        $newlinkdb = date("Y-m-d", $args['selectdate']);

        $pntable = DBUtil::getTables();
        $column = $pntable['links_links_column'];
        $column2 = $pntable['links_categories_column'];

        $where = "WHERE $column[date] LIKE '%$newlinkdb%' AND $column[cat_id] = $column2[cat_id]";

        return DBUtil::selectObjectCount('links_links', $where);
    }

    /**
    * get weblinks by day
    */
    public function weblinksbydate($args)
    {


        if (!isset($args['selectdate']) || !is_numeric($args['selectdate'])) {
            return LogUtil::registerArgsError();
        }

        $pntable = DBUtil::getTables();
        $newlinkdb = date("Y-m-d", $args['selectdate']);
        $column = $pntable['links_links_column'];
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
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return $weblinks;
    }

    /**
    * add broken link to db
    */
    public function addbrockenlink($args)
    {


        // Argument check
        if (!isset($args['lid']) || !is_numeric($args['lid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!ModUtil::getVar('Weblinks', 'unregbroken') == 1 &&
            !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $items = array('lid' => $args['lid'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 1);
        if (!DBUtil::insertObject($items, 'links_modrequest', 'requestid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
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
        if (!ModUtil::getVar('Weblinks', 'blockunregmodify') == 1 &&
            !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $items = array('lid' => $args['lid'], 'cat_id' => $args['cid'], 'title' => $args['title'], 'url' => $args['url'], 'description' => $args['description'], 'modifysubmitter' => $args['submitter'], 'brokenlink' => 0);
        if (!DBUtil::insertObject($items, 'links_modrequest', 'requestid')) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return true;
    }

    /**
    * add link to db
    */
    public function add($args)
    {


        // Security check
        if (!ModUtil::getVar('Weblinks', 'links_anonaddlinklock') == 1 &&
            !SecurityUtil::checkPermission('Weblinks::', "::", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError();
        }

        $checkurl = ModUtil::apiFunc('Weblinks', 'user', 'checkurl', array('url' => $args['url']));
        $valid = System::varValidate($args['url'], 'url');

        if ($checkurl > 0) {
            $link['text'] = $this->__('Sorry! This URL is already listed in the database!');
            $link['submit'] = 0;
            return $link;
        } else if ($valid == false) {
            $link['text'] = $this->__('Sorry! Error! You must type a URL for the web link!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($args['title'])) {
            $link['text'] = $this->__('Sorry! Error! You must type a title for the URL!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($args['cat']) || !is_numeric($args['cat'])) {
            $link['text'] =$this->__('Sorry! Error! No category!');
            $link['submit'] = 0;
            return $link;
        } else if (empty($args['description'])) {
            $link['text'] =$this->__('Sorry! Error! You must type a description for the URL!');
            $link['submit'] = 0;
            return $link;
        } else {
            if (empty($args['submitter'])) {
                $link['submitter'] = System::getVar("anonymous");
            }

            $items = array('cat_id' => $args['cat'], 'title' => $args['title'],'url' => $args['url'], 'description' => $args['description'], 'name' => $args['submitter'], 'email' => $args['submitteremail'], 'submitter' => $args['submitter']);
            if (!DBUtil::insertObject($items, 'links_newlink', 'lid')) {
                return LogUtil::registerError($this->__('Error! Could not load items.'));
            }

            if (empty($args['submitteremail'])) {
                $link['text'] = $this->__('You didn\'t enter an e-mail address. However, your link will still be checked.');
            } else {
                $link['text'] = $this->__('Thank you! You\'ll receive an e-mail message when it\'s approved.');
            }
            $link['submit'] = 1;
            return $link;
        }
    }

    /**
    * check if url exists
    */
    public function checkurl($args)
    {
        return DBUtil::selectObjectCountByID('links_links', $args['url'], 'url', 'lower');
    }

    /**
    * get the last weblinks
    */
    public function lastweblinks($args)
    {


        // Argument check
        if (!isset($args['lastlinks']) || !is_numeric($args['lastlinks'])) {
            $args['lastlinks'] = ModUtil::getVar('Weblinks', 'linksinblock');
        }

        $pntable = DBUtil::getTables();
        $weblinkscolumn = $pntable['links_links_column'];

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
        if ($lastweblinks === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the last weblinks
        return $lastweblinks;
    }

    /**
    * get the most popular weblinks
    */
    public function mostpopularweblinks($args)
    {


        // Argument check
        if (!isset($args['mostpoplinks']) || !is_numeric($args['mostpoplinks'])) {
            $args['mostpoplinks'] = ModUtil::getVar('Weblinks', 'linksinblock');
        }

        $pntable = DBUtil::getTables();
        $weblinkscolumn = $pntable['links_links_column'];

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
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // return the most popular weblinks
        return $mostpopularweblinks;
    }
}