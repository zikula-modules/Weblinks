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
 * Search plugin info
 **/
function Weblinks_searchapi_info()
{
    return array('title' => 'Weblinks', 'functions' => array('Weblinks' => 'search'));
}

/**
 * Search form component
 **/
function Weblinks_searchapi_options($args)
{
    if (SecurityUtil::checkPermission('Weblinks::', '::', ACCESS_READ)) {
        // Create output object - this object will store all of our output so that
        // we can return it easily when required
        $pnRender = & pnRender::getInstance('Weblinks', false);
        $pnRender->assign('active',(isset($args['active'])&&isset($args['active']['Weblinks']))||(!isset($args['active'])));
        return $pnRender->fetch('weblinks_search_options.htm');
    }

    return '';
}

/**
 * Search plugin main function
 **/
function Weblinks_searchapi_search($args)
{
    if (!SecurityUtil::checkPermission( 'Weblinks::', '::', ACCESS_READ)) {
        return true;
    }

    pnModDBInfoLoad('Search');
    $pntable = pnDBGetTables();
    $linkstable = $pntable['links_links'];
    $linkscolumn = $pntable['links_links_column'];
    $searchTable = $pntable['search_result'];
    $searchColumn = $pntable['search_result_column'];

    $where = search_construct_where($args,
                                    array($linkscolumn['title'],
                                          $linkscolumn['description']),
                                          null);

    $sessionId = session_id();

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

    // get the result set
    $links = DBUtil::selectObjectArray('links_links', $where, 'lid', 1, -1, '', $permFilter);
    if ($links === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    $insertSql = "INSERT INTO $searchTable ($searchColumn[title],
                                            $searchColumn[text],
                                            $searchColumn[extra],
                                            $searchColumn[module],
                                            $searchColumn[created],
                                            $searchColumn[session]) VALUES ";

    foreach ($links as $link)
    {
          $sql = $insertSql . '('
                 . '\'' . DataUtil::formatForStore($link['title']) . '\', '
                 . '\'' . DataUtil::formatForStore($link['description']) . '\', '
                 . '\'' . DataUtil::formatForStore($link['lid']) . '\', '
                 . '\'' . 'Weblinks' . '\', '
                 . '\'' . DataUtil::formatForStore($link['date']) . '\', '
                 . '\'' . DataUtil::formatForStore($sessionId) . '\')';
          $insertResult = DBUtil::executeSQL($sql);
          if (!$insertResult) {
              return LogUtil::registerError (_GETFAILED);
          }
    }

    return true;
}


/**
 * Do last minute access checking and assign URL to items
 *
 * Access checking is ignored since access check has
 * already been done. But we do add a URL to the found user
 */
function Weblinks_searchapi_search_check(&$args)
{
    $datarow = &$args['datarow'];
    $linkId = $datarow['extra'];

    $datarow['url'] = pnModUrl('Weblinks', 'user', 'visit', array('lid' => $linkId));

    return true;
}