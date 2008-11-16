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

/**
*
*/
function Web_Links_userapi_categories()
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError ();
    }

    // define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Category',
                              'instance_left'   => 'title',
                              'instance_right'  => 'cat_id',
                              'level'           => ACCESS_READ));

	// get the objects from the db
    $objArray = DBUtil::selectObjectArray('links_categories', '', '', '-1', '-1', '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}

function Web_Links_userapi_category($args)
{
    // Argument check
    if ((!isset($args['cid']) || empty($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError ();
    }

	// define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Category',
                              'instance_left'   => 'title',
                              'instance_right'  => 'cat_id',
                              'level'           => ACCESS_READ));

	// get the object from the db
    $objArray = DBUtil::selectObjectById('links_categories', $args['cid'], 'cat_id', '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}

function Web_Links_userapi_subcategory($args)
{
    // Argument check
    if ((!isset($args['cid']) || empty($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError ();
    }

	$objArray = array();

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_categories_column'];

	$where = "WHERE $weblinkscolumn[parent_id] = ".(int)DataUtil::formatForStore($args['cid']);

    // define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Category',
                              'instance_left'   => 'title',
                              'instance_right'  => 'cat_id',
                              'level'           => ACCESS_READ));

	// get the objects from the db
    $objArray = DBUtil::selectObjectArray('links_categories', $where, 'title', '-1', '-1', '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}

function Web_Links_userapi_weblinks($args)
{
    // Argument check
    if ((!isset($args['cid']) || empty($args['cid']) || !is_numeric($args['cid']))) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    if (!isset($args['startnum']) || empty($args['startnum'])) {
        $args['startnum'] = 1;
    }
    if (!isset($args['numlinks']) || empty($args['numlinks'])) {
        $args['numlinks'] = -1;
    }

    if (!is_numeric($args['startnum']) ||
        !is_numeric($args['numlinks'])) {
        return LogUtil::registerError (_MODARGSERROR);
    }

	$objArray = array();

    $pntable = pnDBGetTables();
    $weblinkscolumn = &$pntable['links_links_column'];

    $where = "WHERE $weblinkscolumn[cat_id] = ".(int)DataUtil::formatForStore($args['cid']);

    // define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Link',
                              'instance_left'   => 'title',
                              'instance_right'  => 'lid',
                              'level'           => ACCESS_READ));

	// get the objects from the db
    $objArray = DBUtil::selectObjectArray('links_links', $where, $args['orderbysql'], $args['startnum']-1, $args['numlinks'], '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}

function Web_Links_userapi_orderbyin($args)
{
    extract($args);

    $pntable =& pnDBGetTables();
    $column = &$pntable['links_links_column'];

    if ($orderby == "titleA") {
		$orderbysql = "$column[title] ASC";
    } else
    if ($orderby == "dateA") {
		$orderbysql = "$column[date] ASC";
    } else
    if ($orderby == "hitsA") {
		$orderbysql = "$column[hits] ASC";
    } else
    if ($orderby == "ratingA") {
		$orderbysql = "$column[linkratingsummary] ASC";
    } else
    if ($orderby == "titleD") {
		$orderbysql = "$column[title] DESC";
    } else
    if ($orderby == "dateD") {
		$orderbysql = "$column[date] DESC";
    } else
    if ($orderby == "hitsD") {
		$orderbysql = "$column[hits] DESC";
    } else
    if ($orderby == "ratingD") {
		$orderbysql = "$column[linkratingsummary] DESC";
    } else {
		$orderbysql = "$column[title] ASC";
	}
	return $orderbysql;
}

function Web_Links_userapi_countcatlinks($args)
{
	extract($args);

    if ((!isset($cid) || !is_numeric($cid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
    $sql = "SELECT $column[lid], $column[cat_id], $column[title]
            FROM $pntable[links_links]
            WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
	$result =& $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    $numlinks = 0;
    for (; !$result->EOF; $result->MoveNext()) {
        list($lid, $cid, $title) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_OVERVIEW) ||
    		SecurityUtil::checkPermission('Web_Links::Item', "$title::$lid", ACCESS_OVERVIEW)) {
            $numlinks++;
        }
    }

    $result->Close();

    return $numlinks;
}

function Web_Links_userapi_link($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $weblinkstable = $pntable['links_links'];
    $weblinkscolumn = &$pntable['links_links_column'];

    $sql = "SELECT $weblinkscolumn[lid],
    			   $weblinkscolumn[cat_id],
                   $weblinkscolumn[title],
                   $weblinkscolumn[url],
				   $weblinkscolumn[description],
				   $weblinkscolumn[date],
				   $weblinkscolumn[name],
				   $weblinkscolumn[email],
				   $weblinkscolumn[hits]
            FROM $weblinkstable
            WHERE $weblinkscolumn[lid] = '" . (int)DataUtil::formatForStore($lid) . "'";
    $result =& $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return false;
    }

    // Obtain the item information from the result set
    list($lid, $cat_id, $title, $url, $description, $date, $name, $email, $hits) = $result->fields;

    $result->Close();

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', "$title::$lid", ACCESS_READ)) {
        return LogUtil::registerPermissionError ();
    }

    // Create the link array
    $link = array('lid' => $lid,
    			  'cat_id' => $cat_id,
                  'title' => $title,
                  'url' => $url,
				  'description' => $description,
				  'date' => $date,
				  'name' => $name,
				  'email' => $email,
				  'hits' => $hits);

    // Return the link array
    return $link;
}

function Web_Links_userapi_hitcountinc($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::Item', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $weblinkstable = $pntable['links_links'];
    $weblinkscolumn = &$pntable['links_links_column'];

    // Update the item
    $sql = "UPDATE $weblinkstable
            SET $weblinkscolumn[hits] = $weblinkscolumn[hits] + 1
            WHERE $weblinkscolumn[lid] = " . DataUtil::formatForStore($lid);
    $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        // We probably don't want to display a user error here
        //pnSessionSetVar('errormsg', _WEBLINKSUPDATEDCOUNTFAILED);
        return false;
    }

    return true;

}

function Web_Links_userapi_random()
{
    $totallinks = 0;

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];

    $sql = "SELECT $column[cat_id], $column[title]
    		FROM $pntable[links_links]";
    $result =& $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    while(list($cid, $title)=$result->fields) {
		$result->MoveNext();
		if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_READ)) {
			$totallinks++;
		}
    }

    $result->Close();

    $numrows = $totallinks;

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

function Web_Links_userapi_searchcats($args)
{
    // Get arguments from argument array
    extract($args);

	$subcategory = array();

    // Argument check
    if (!isset($query)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // It's good practice to name the table and column definitions you are getting
    $weblinkstable = $pntable['links_categories'];
    $weblinkscolumn = &$pntable['links_categories_column'];

    // Get categories
    $sql = "SELECT $weblinkscolumn[title],
    			   $weblinkscolumn[cat_id]
    	    FROM $weblinkstable
    	    WHERE $weblinkscolumn[title] LIKE '%".DataUtil::formatForStore($query)."%'
    	    ORDER BY $weblinkscolumn[title] DESC";
    $result =& $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _GETFAILED);
        return false;
    }

    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($title, $cat_id) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cat_id", ACCESS_READ)) {
            $subcategory[] = array('title' => $title,
                            	   'cat_id' => $cat_id);
        }
    }

    // All successful database queries produce a result set
    $result->Close();

    // Return the items
    return $subcategory;
}

function Web_Links_userapi_search_weblinks($args)
{
	// get arguments from argument array
	extract($args);

    // Argument check
    if (!isset($query)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Optional arguments.
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }

    if (!isset($numlinks) || !is_numeric($numlinks)) {
        $numlinks = -1;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
    $sql = "SELECT $column[lid],
    			   $column[cat_id],
    			   $column[title],
    			   $column[url],
    			   $column[description],
    			   $column[date],
                   $column[hits],
                   $column[linkratingsummary],
                   $column[totalvotes],
                   $column[totalcomments]
            FROM $pntable[links_links]
            WHERE $column[title] LIKE '%".DataUtil::formatForStore($query)."%'
            OR $column[description] LIKE '%".DataUtil::formatForStore($query)."%'
            ORDER BY $orderbysql";
    $result =& $dbconn->SelectLimit($sql, $numlinks, $startnum-1);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($lid, $cat_id, $title, $url, $description, $time, $hits, $linkratingsummary, $totalvotes, $totalcomments) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Link', ":$title:$lid", ACCESS_READ)) {
            $weblinks[] = array('lid' => $lid,
                                'cat_id' => $cat_id,
                                'title' => $title,
                                'url' => $url,
                                'description' => $description,
                                'time' => $time,
                                'hits' => $hits,
                                'linkratingsummary' => number_format($linkratingsummary, pnModGetVar('Web_Links', 'mainvotedecimal')),
                                'totalvotes' => $totalvotes,
                                'totalcomments' => $totalcomments);
		}
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $weblinks;
}

function Web_Links_userapi_countsearchlinks($args)
{
	extract($args);

    if (!isset($query)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
    $sql = "SELECT count(*)
            FROM $pntable[links_links]
            WHERE $column[title] LIKE '%".DataUtil::formatForStore($query)."%'
            OR $column[description] LIKE '%".DataUtil::formatForStore($query)."%'";
	$result =& $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

	list($numlinks) = $result->fields;

    $result->Close();

    return $numlinks;
}

function Web_Links_userapi_totallinks($args)
{
    extract($args);

    if (!isset($selectdate) || !is_numeric($selectdate)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $dateDB = (date("d-M-Y", $selectdate));

    $newlinkdb = date("Y-m-d", $selectdate);
    $column = &$pntable['links_links_column'];
	$column2 = &$pntable['links_categories_column'];
    //$result =& $dbconn->Execute("SELECT COUNT(*) FROM $pntable[links_links] WHERE $column[date] LIKE '%".DataUtil::formatForStore($newlinkDB)."%'");
   	$totallinks=0;
	$result =& $dbconn->Execute("SELECT $column[cat_id], $column2[title]
							FROM $pntable[links_links], $pntable[links_categories]
							WHERE $column[date] LIKE '%$newlinkdb%'
							AND $column[cat_id]=$column2[cat_id]");

    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

	while(list($cid, $title)=$result->fields) {
       	$result->MoveNext();
       	if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_READ)) {
       		$totallinks++;
       	}
	}

    $result->Close();

    return $totallinks;
}

function Web_Links_userapi_weblinksbydate($args)
{
    extract($args);

    if (!isset($selectdate) || !is_numeric($selectdate)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $newlinkdb = date("Y-m-d", $selectdate);

    $column = &$pntable['links_links_column'];
    $sql = "SELECT $column[lid],
    			   $column[cat_id],
    			   $column[title],
    			   $column[description],
    			   $column[date],
                   $column[hits],
                   $column[linkratingsummary],
                   $column[totalvotes],
                   $column[totalcomments]
            FROM $pntable[links_links]
            WHERE $column[date] LIKE '%".DataUtil::formatForStore($newlinkdb)."%'
            ORDER BY $column[title] ASC";
    $result =& $dbconn->SelectLimit($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($lid, $cid, $title, $description, $time, $hits, $linkratingsummary, $totalvotes, $totalcomments) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_READ) ||
    		SecurityUtil::checkPermission('Web_Links::Item', "$title::$lid", ACCESS_READ)) {
            $weblinks[] = array('lid' => $lid,
            					'cid' => $cid,
                                'title' => $title,
                                'description' => $description,
                                'time' => $time,
                                'hits' => $hits,
                                'linkratingsummary' => $linkratingsummary,
                                'totalvotes' => $totalvotes,
                                'totalcomments' => $totalcomments);
		}
    }

	$result->Close();

	return $weblinks;
}

function Web_Links_userapi_weblinksmostpop($args)
{
    extract($args);

    if (!isset($mostpoplinks) || !is_numeric($mostpoplinks)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
    $sql = "SELECT $column[lid],
    			   $column[cat_id],
    			   $column[title],
    			   $column[description],
    			   $column[date],
                   $column[hits],
                   $column[linkratingsummary],
                   $column[totalvotes],
                   $column[totalcomments]
            FROM $pntable[links_links]
				ORDER BY $column[hits] DESC
				LIMIT $mostpoplinks";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($lid, $cid, $title, $description, $time, $hits, $linkratingsummary, $totalvotes, $totalcomments) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_READ) ||
    		SecurityUtil::checkPermission('Web_Links::Item', "$title::$lid", ACCESS_READ)) {
            $weblinks[] = array('lid' => $lid,
            					'cid' => $cid,
                                'title' => $title,
                                'description' => $description,
                                'time' => $time,
                                'hits' => $hits,
                                'linkratingsummary' => number_format($linkratingsummary, pnModGetVar('Web_Links', 'mainvotedecimal')),
                                'totalvotes' => $totalvotes,
                                'totalcomments' => $totalcomments);
		}
    }

	$result->Close();

	return $weblinks;
}

function Web_Links_userapi_weblinkstoprated($args)
{
    extract($args);

    if (!isset($toplinks) || !is_numeric($toplinks)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    if (!isset($linkvotemin) || !is_numeric($linkvotemin)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
    $sql = "SELECT $column[lid],
    			   $column[cat_id],
    			   $column[title],
    			   $column[description],
    			   $column[date],
                   $column[hits],
                   $column[linkratingsummary],
                   $column[totalvotes],
                   $column[totalcomments]
            FROM $pntable[links_links]
			WHERE $column[linkratingsummary] != 0 AND $column[totalvotes] >= $linkvotemin
			ORDER BY $column[linkratingsummary] DESC";
    $result =& $dbconn->SelectLimit($sql, $toplinks, 0);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($lid, $cid, $title, $description, $time, $hits, $linkratingsummary, $totalvotes, $totalcomments) = $result->fields;
        if (SecurityUtil::checkPermission('Web_Links::Category', "$title::$cid", ACCESS_READ) ||
    		SecurityUtil::checkPermission('Web_Links::Item', "$title::$lid", ACCESS_READ)) {
            $weblinks[] = array('lid' => $lid,
            					'cid' => $cid,
                                'title' => $title,
                                'description' => $description,
                                'time' => $time,
                                'hits' => $hits,
                                'linkratingsummary' => number_format($linkratingsummary, pnModGetVar('Web_Links', 'mainvotedecimal')),
                                'totalvotes' => $totalvotes,
                                'totalcomments' => $totalcomments);
		}
    }

	$result->Close();

	return $weblinks;
}

function Web_Links_userapi_brockenlink($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', "::", ACCESS_READ)) {
		return LogUtil::registerPermissionError();
    }

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $nextid = $dbconn->GenId($pntable['links_modrequest']);
    $column = &$pntable['links_modrequest_column'];
    $sql ="INSERT INTO $pntable[links_modrequest]
    			  	  ($column[requestid],
    			  	   $column[lid],
    			  	   $column[modifysubmitter],
    			  	   $column[brokenlink])
           VALUES ($nextid,
           		  ".(int)DataUtil::formatForStore($lid).",
           		  '".DataUtil::formatForStore($modifysubmitter)."',
           		  1)";
    $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        // We probably don't want to display a user error here
        //pnSessionSetVar('errormsg', _WEBLINKSUPDATEDCOUNTFAILED);
        return false;
    }

    return true;
}

function Web_Links_userapi_modifylinkrequest($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $nextid = $dbconn->GenId($pntable['links_modrequest']);
    $column = &$pntable['links_modrequest_column'];
    $sql ="INSERT INTO $pntable[links_modrequest]
    			  	  ($column[requestid],
    			  	   $column[lid],
    			  	   $column[cat_id],
    			  	   $column[title],
    			  	   $column[url],
    			  	   $column[description],
    			  	   $column[modifysubmitter],
    			  	   $column[brokenlink])
           VALUES ($nextid,
           		  '".(int)DataUtil::formatForStore($lid)."',
           		  '".DataUtil::formatForStore($cat)."',
           		  '".DataUtil::formatForStore($title)."',
           		  '".DataUtil::formatForStore($url)."',
           		  '".DataUtil::formatForStore($description)."',
           		  '".DataUtil::formatForStore($modifysubmitter)."',
           		  0)";
    $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        // We probably don't want to display a user error here
        //pnSessionSetVar('errormsg', _WEBLINKSUPDATEDCOUNTFAILED);
        return false;
    }

    return true;
}

function Web_Links_userapi_existingurl($args)
{
	// Get datbase setup
	$dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$column = &$pntable['links_links_column'];
    $sql = "SELECT $column[title]
    	    FROM $pntable[links_links]
    	    WHERE $column[url]='" . DataUtil::formatForStore($args['url']) . "'";
    $existingurl =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    if (!$existingurl->EOF) {
    	$existingurl = 1;
    } else {
    	$existingurl = 0;
    }

    return $existingurl;
}

function Web_Links_userapi_add($args)
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', "::", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError();
    }

	$link = array();

	$existingurl = pnModAPIFunc('Web_Links', 'user', 'existingurl', array('url' => $args['url']));
	$valid = pnVarValidate($args['url'], 'url');

    if ($existingurl == 1) {
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
	} else if (empty($args['cat']) || !is_numeric($args['cat'])) {
	    $link['text'] =_WL_LINKNOCAT;
	    $link['submit'] = 0;
	    return $link;
	} else if (empty($args['description'])) {
	    $link['text'] =_WL_LINKNODESC;
	    $link['submit'] = 0;
	    return $link;
	} else {
		if (pnUserLoggedIn()) {
    		$submitter = pnUserGetVar('uname');
    	}

        // Get datbase setup
        $dbconn =& pnDBGetConn(true);
        $pntable =& pnDBGetTables();

    	$column = &$pntable['links_newlink_column'];
    	$nextid = $dbconn->GenId($pntable['links_newlink']);
    	$dbconn->Execute("INSERT INTO $pntable[links_newlink] ($column[lid], $column[cat_id], $column[title], $column[url], $column[description], $column[name], $column[email], $column[submitter]) VALUES ($nextid, ".(int)pnVarPrepForStore($args['cat']).", '".pnVarPrepForStore($args['title'])."', '".pnVarPrepForStore($args['url'])."', '".pnVarPrepForStore($args['description'])."', '".pnVarPrepForStore($args['nname'])."', '".pnVarPrepForStore($args['email'])."', '".pnVarPrepForStore($submitter)."')");

    	if (empty($args['email'])) {
        	$link['text'] = _WL_CHECKFORIT;
    	} else {
        	$link['text'] = _WL_EMAILWHENADD;
    	}
    	$link['submit'] = 1;
    	return $link;
    }
}

function Web_Links_userapi_displaytitle($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
	$sql = "SELECT $column[title]
            FROM   $pntable[links_links]
            WHERE  $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    list($displaytitle) = $result->fields;

    $result->Close();

    return $displaytitle;
}

function Web_Links_userapi_totalcomments($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$column = &$pntable['links_votedata_column'];
	$sql = "SELECT $column[ratinguser],
				   $column[rating],
				   $column[ratingcomments],
				   $column[ratingtimestamp]
			FROM $pntable[links_votedata]
			WHERE $column[ratinglid]='".(int)DataUtil::formatForStore($lid)."'
			AND $column[ratingcomments] != '' ORDER BY $column[ratingtimestamp] DESC";
	$result =& $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        // We probably don't want to display a user error here
        //pnSessionSetVar('errormsg', _WEBLINKSUPDATEDCOUNTFAILED);
        return false;
    }

	$numofcomments = $result->PO_RecordCount();

    for (; !$result->EOF; $result->MoveNext()) {
    	list($ratinguser, $rating, $ratingcomments, $ratingtimestamp) = $result->fields;
    	    $column = &$pntable['links_votedata_column'];
    	    $sql = "SELECT SUM($column[rating]),
    	    		COUNT(*) FROM $pntable[links_votedata]
    	    		WHERE $column[ratinguser]='".DataUtil::formatForStore($ratinguser)."'";
        	$result2 =& $dbconn->Execute($sql);
        	list($useravgrating, $usertotalcomments)=$result2->fields;
        	$useravgrating = $useravgrating / $usertotalcomments;
        	$useravgrating = number_format($useravgrating, 1);

            $totalcomments[] = array('ratinguser' => $ratinguser,
            						 'rating' => $rating,
            						 'ratingcomments' => $ratingcomments,
                                     'ratingtimestamp' => $ratingtimestamp,
                                     'useravgrating' => $useravgrating,
                                     'usertotalcomments' => $usertotalcomments);
    }

	$result->Close();

	$comments = array('numofcomments' => $numofcomments,
					  'totalcomments' => $totalcomments);

	return $comments;
}

function Web_Links_userapi_editorial($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_editorials_column'];
    $sql = "SELECT $column[adminid],
    			   $column[editorialtimestamp],
    			   $column[editorialtext],
    			   $column[editorialtitle]
            FROM $pntable[links_editorials]
            WHERE $column[linkid]=".(int)DataUtil::formatForStore($lid)."";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return false;
    }

    list($adminid, $editorialtimestamp, $editorialtext, $editorialtitle) = $result->fields;

    $result->Close();

    $editorial = array('adminid' => $adminid,
    		     	   'editorialtimestamp' => $editorialtimestamp,
    		     	   'editorialtext' => $editorialtext,
    		     	   'editorialtitle' => $editorialtitle);

    return $editorial;
}

function Web_Links_userapi_numrows()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$sql = "SELECT COUNT(*) FROM $pntable[links_links]";
	$result =& $dbconn->Execute($sql);
    list($numrows) = $result->fields;

	return $numrows;
}

function Web_Links_userapi_catnum()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$sql = "SELECT COUNT(*) FROM $pntable[links_categories]";
	$result =& $dbconn->Execute($sql);
    list($catnum) = $result->fields;

    return $catnum;
}

function Web_Links_userapi_votes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_links_column'];
	$column2 = &$pntable['links_categories_column'];
	$sql = "SELECT $column[cat_id], $column[description], $column2[title]
                           FROM $pntable[links_links], $pntable[links_categories]
                           WHERE $column[lid]='".DataUtil::formatForStore($lid)."'
						   AND $column[cat_id]=$column2[cat_id]";
    $res =& $dbconn->Execute($sql);
    list($cid, $description, $title) = $res->fields;

/*    if (!pnSecAuthAction(0, 'Web Links::Category', "$title::$cid" , ACCESS_READ)) {
		echo _BADAUTHKEY;
		include 'footer.php';
		return;
	}
*/
    $useoutsidevoting = pnConfigGetVar('useoutsidevoting');
    $anonymous = pnConfigGetVar('anonymous');
    $detailvotedecimal = pnConfigGetVar('detailvotedecimal');
    $anonweight = pnConfigGetVar('anonweight');
    $outsideweight = pnConfigGetVar('anonweight');

    $column = &$pntable['links_votedata_column'];
    $voteresult =& $dbconn->Execute("SELECT $column[rating], $column[ratinguser],
                                  $column[ratingcomments]
                                  FROM $pntable[links_votedata]
                                  WHERE $column[ratinglid]='".(int)DataUtil::formatForStore($lid)."'");
    $totalvotesDB = $voteresult->PO_RecordCount();
    $anonvotes = 0;
    $anonvoteval = 0;
    $outsidevotes = 0;
    $outsidevoteeval = 0;
    $regvoteval = 0;
    $topanon = 0;
    $bottomanon = 11;
    $topreg = 0;
    $bottomreg = 11;
    $topoutside = 0;
    $bottomoutside = 11;
    $avv = array(0,0,0,0,0,0,0,0,0,0,0);
    $rvv = array(0,0,0,0,0,0,0,0,0,0,0);
    $ovv = array(0,0,0,0,0,0,0,0,0,0,0);
    $truecomments = $totalvotesDB;
    while(list($ratingDB, $ratinguserDB, $ratingcommentsDB) = $voteresult->fields) {

        $voteresult->MoveNext();
        if ($ratingcommentsDB==""){
            $truecomments--;
        }
        if ($ratinguserDB==pnConfigGetVar("anonymous")) {
            $anonvotes++;
            $anonvoteval += $ratingDB;
        }
        if (isset($useoutsidevoting) && $useoutsidevoting == 1) {
            if ($ratinguserDB=='outside') {
                $outsidevotes++;
                $outsidevoteval += $ratingDB;
            }
        } else {
            $outsidevotes = 0;
        }
        if ($ratinguserDB != pnConfigGetVar('anonymous') && $ratinguserDB!="outside") {
            $regvoteval += $ratingDB;
        }
        if ($ratinguserDB != pnConfigGetVar('anonymous') && $ratinguserDB!="outside") {
            if ($ratingDB > $topreg) {
                $topreg = $ratingDB;
            }
            if ($ratingDB < $bottomreg) {
                $bottomreg = $ratingDB;
            }
            for ($rcounter=1; $rcounter<11; $rcounter++) {
                if ($ratingDB==$rcounter) {
                    $rvv[$rcounter]++;
                }
            }
        }
        if ($ratinguserDB==pnConfigGetVar("anonymous")) {
            if ($ratingDB > $topanon) {
                $topanon = $ratingDB;
            }
            if ($ratingDB < $bottomanon) {
                $bottomanon = $ratingDB;
            }
            for ($rcounter=1; $rcounter<11; $rcounter++) {
                if ($ratingDB==$rcounter) {
                    $avv[$rcounter]++;
                }
            }
        }
        if ($ratinguserDB=="outside") {
            if ($ratingDB > $topoutside) {
                $topoutside = $ratingDB;
            }
            if ($ratingDB < $bottomoutside) {
                $bottomoutside = $ratingDB;
            }
            for ($rcounter=1; $rcounter<11; $rcounter++) {
                if ($ratingDB==$rcounter) {
                    $ovv[$rcounter]++;
                }
            }
        }
    }
    $regvotes = $totalvotesDB - $anonvotes - $outsidevotes;
    $avgRU = 0;
    $avgAU = 0;
    $avgOU = 0;
    if ($totalvotesDB == 0) {
        $finalrating = 0;
    } else if ($anonvotes == 0 && $regvotes == 0) {
        /* Figure Outside Only Vote */
        $finalrating = $outsidevoteval / $outsidevotes;
        $finalrating = number_format($finalrating, $detailvotedecimal);
        $avgOU = $outsidevoteval / $totalvotesDB;
        $avgOU = number_format($avgOU, $detailvotedecimal);
    } else if ($outsidevotes == 0 && $regvotes == 0) {
        /* Figure Anon Only Vote */
        $finalrating = $anonvoteval / $anonvotes;
        $finalrating = number_format($finalrating, $detailvotedecimal);
        $avgAU = $anonvoteval / $totalvotesDB;
        $avgAU = number_format($avgAU, $detailvotedecimal);
    } else if ($outsidevotes == 0 && $anonvotes == 0) {
        /* Figure Reg Only Vote */
        $finalrating = $regvoteval / $regvotes;
        $finalrating = number_format($finalrating, $detailvotedecimal);
        $avgRU = $regvoteval / $totalvotesDB;
        $avgRU = number_format($avgRU, $detailvotedecimal);
    } else if ($regvotes == 0 && $useoutsidevoting == 1 && $outsidevotes != 0 && $anonvotes != 0 ) {
        /* Figure Reg and Anon Mix */
        $avgAU = $anonvoteval / $anonvotes;
        $avgOU = $outsidevoteval / $outsidevotes;
        if ($anonweight > $outsideweight ) {
            /* Anon is 'standard weight' */
            $newimpact = $anonweight / $outsideweight;
            $impactAU = $anonvotes;
            $impactOU = $outsidevotes / $newimpact;
            $finalrating = ((($avgOU * $impactOU) + ($avgAU * $impactAU)) / ($impactAU + $impactOU));
            $finalrating = number_format($finalrating, $detailvotedecimal);
        } else {
            /* Outside is 'standard weight' */
            $newimpact = $outsideweight / $anonweight;
            $impactOU = $outsidevotes;
            $impactAU = $anonvotes / $newimpact;
            $finalrating = ((($avgOU * $impactOU) + ($avgAU * $impactAU)) / ($impactAU + $impactOU));
            $finalrating = number_format($finalrating, $detailvotedecimal);
        }
    } else {
        /* REG User vs. Anonymous vs. Outside User Weight Calutions */
        $impact = $anonweight;
        $outsideimpact = $outsideweight;
        if ($regvotes == 0) {
            $avgRU = 0;
        } else {
            $avgRU = $regvoteval / $regvotes;
        }
        if ($anonvotes == 0) {
            $avgAU = 0;
        } else {
            $avgAU = $anonvoteval / $anonvotes;
        }
        if ($outsidevotes == 0 ) {
            $avgOU = 0;
        } else {
            $avgOU = $outsidevoteval / $outsidevotes;
        }
        $impactRU = $regvotes;
        $impactAU = $anonvotes / $impact;
        $impactOU = $outsidevotes / $outsideimpact;
        $finalrating = (($avgRU * $impactRU) + ($avgAU * $impactAU) + ($avgOU * $impactOU)) / ($impactRU + $impactAU + $impactOU);
        $finalrating = number_format($finalrating, $detailvotedecimal);
    }
    if (!isset($avgOU) || $avgOU == 0 || $avgOU == "") {
        $avgOU = "";
    } else {
        $avgOU = number_format($avgOU, $detailvotedecimal);
    }
    if ($avgRU == 0 || $avgRU == "") {
        $avgRU = "";
    } else {
        $avgRU = number_format($avgRU, $detailvotedecimal);
    }
    if (!isset($avgAU) || $avgAU == 0 || $avgAU == "") {
        $avgAU = "";
    } else {
        $avgAU = number_format($avgAU, $detailvotedecimal);
    }
    if ($topanon == 0) $topanon = "";
    if ($bottomanon == 11) $bottomanon = "";
    if ($topreg == 0) $topreg = "";
    if ($bottomreg == 11) $bottomreg = "";
    if ($topoutside == 0) $topoutside = "";
    if ($bottomoutside == 11) $bottomoutside = "";
    $totalchartheight = 70;
    $chartunits = $totalchartheight / 10;
    $avvper     = array(0,0,0,0,0,0,0,0,0,0,0);
    $rvvper         = array(0,0,0,0,0,0,0,0,0,0,0);
    $ovvper         = array(0,0,0,0,0,0,0,0,0,0,0);
    $avvpercent     = array(0,0,0,0,0,0,0,0,0,0,0);
    $rvvpercent     = array(0,0,0,0,0,0,0,0,0,0,0);
    $ovvpercent     = array(0,0,0,0,0,0,0,0,0,0,0);
    $avvchartheight = array(0,0,0,0,0,0,0,0,0,0,0);
    $rvvchartheight = array(0,0,0,0,0,0,0,0,0,0,0);
    $ovvchartheight = array(0,0,0,0,0,0,0,0,0,0,0);
    $avvmultiplier = 0;
    $rvvmultiplier = 0;
    $ovvmultiplier = 0;
    for ($rcounter=1; $rcounter<11; $rcounter++) {
        if ($anonvotes != 0) $avvper[$rcounter] = $avv[$rcounter] / $anonvotes;
        if ($regvotes != 0) $rvvper[$rcounter] = $rvv[$rcounter] / $regvotes;
        if ($outsidevotes != 0) $ovvper[$rcounter] = $ovv[$rcounter] / $outsidevotes;
        $avvpercent[$rcounter] = number_format($avvper[$rcounter] * 100, 1);
        $rvvpercent[$rcounter] = number_format($rvvper[$rcounter] * 100, 1);
        $ovvpercent[$rcounter] = number_format($ovvper[$rcounter] * 100, 1);
        if ($avv[$rcounter] > $avvmultiplier) $avvmultiplier = $avv[$rcounter];
        if ($rvv[$rcounter] > $rvvmultiplier) $rvvmultiplier = $rvv[$rcounter];
        if ($ovv[$rcounter] > $ovvmultiplier) $ovvmultiplier = $ovv[$rcounter];
    }
    if ($avvmultiplier != 0) $avvmultiplier = 10 / $avvmultiplier;
    if ($rvvmultiplier != 0) $rvvmultiplier = 10 / $rvvmultiplier;
    if ($ovvmultiplier != 0) $ovvmultiplier = 10 / $ovvmultiplier;
    for ($rcounter=1; $rcounter<11; $rcounter++) {
        $avvchartheight[$rcounter] = ($avv[$rcounter] * $avvmultiplier) * $chartunits;
        $rvvchartheight[$rcounter] = ($rvv[$rcounter] * $rvvmultiplier) * $chartunits;
        $ovvchartheight[$rcounter] = ($ovv[$rcounter] * $ovvmultiplier) * $chartunits;
        if ($avvchartheight[$rcounter]==0) $avvchartheight[$rcounter]=1;
        if ($rvvchartheight[$rcounter]==0) $rvvchartheight[$rcounter]=1;
        if ($ovvchartheight[$rcounter]==0) $ovvchartheight[$rcounter]=1;
    }



    $votes = array('totalvotesDB' => $totalvotesDB,
    	     	   'finalrating' => $finalrating,
    	     	   'regvotes' => $regvotes,
    	     	   'rvv' => $rvv,
    	     	   'rvvchartheight' => $rvvchartheight,
    	     	   'rvvpercent' => $rvvpercent,
    	     	   'avgRU' => $avgRU,
    	     	   'topreg' => $topreg,
    	     	   'bottomreg' => $bottomreg,
    	     	   'truecomments' => $truecomments,
    			   'anonvotes' => $anonvotes,
    			   'avv' => $avv,
    	     	   'avvchartheight' => $avvchartheight,
    	     	   'avvpercent' => $avvpercent,
    	     	   'avgAU' => $avgAU,
    	     	   'topanon' => $topanon,
    	     	   'bottomanon' => $bottomanon,
    	     	   'anonweight' => $anonweight,
    	     	   'useoutsidevoting' => $useoutsidevoting,
    	     	   'outsideweight' => $outsideweight,
    	     	   'outsidevotes' => $outsidevotes,
    	     	   'ovv' => $ovv,
    	     	   'ovvchartheight' => $ovvchartheight,
    	     	   'ovvpercent' => $ovvpercent,
    	     	   'avgOU' => $avgOU,
    	     	   'topoutside' => $topoutside,
    	     	   'bottomoutside' => $bottomoutside,
    	     	   );

    return $votes;
}
?>