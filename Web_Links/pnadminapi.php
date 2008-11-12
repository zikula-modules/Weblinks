<?php

/**
 * get available admin panel links
 *
 * @return array array of admin links
 */
function Web_Links_adminapi_getlinks()
{
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH, 403);
    }

    pnModLangLoad('Web_Links', 'admin');

    $links = array();
    $links[] = array('url' => pnModURL('Web_Links', 'admin', 'view'), 		 'text' => _WL_OVERVIEW);
    $links[] = array('url' => pnModURL('Web_Links', 'admin', 'catview'), 	 'text' => _WL_CATVIEW);
    $links[] = array('url' => pnModURL('Web_Links', 'admin', 'linkview'), 	 'text' => _WL_LINKVIEW);
    $links[] = array('url' => pnModURL('Web_Links', 'admin', 'getconfig'),   'text' => _WL_MODCONF);

    return $links;
}

function Web_Links_adminapi_newweblinks()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$column = &$pntable['links_newlink_column'];
    $sql = "SELECT $column[lid],
    			   $column[cat_id],
    			   $column[title],
    			   $column[url],
    			   $column[description],
				   $column[name],
				   $column[email],
				   $column[submitter]
		    FROM $pntable[links_newlink]
			ORDER BY $column[lid]";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($lid, $cid, $title, $url, $description, $name, $email, $submitter) = $result->fields;
    	if ($submitter == "") {
           $submitter = _WL_NONE;
        }
        $newweblinks[] = array('lid' => $lid,
                      		'cid' => $cid,
                      		'title' => $title,
                      		'url' => $url,
                            'description' => $description,
                            'name' => $name,
                            'email' => $email,
                            'submitter' => $submitter);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $newweblinks;
}

function Web_Links_adminapi_getmodlink($args)
{
    // Argument check
    if ((!isset($args['lid']) || !is_numeric($args['lid']))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

	// define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Link',
                              'instance_left'   => 'title',
                              'instance_right'  => 'lid',
                              'level'           => ACCESS_EDIT));

	// get the object from the db
    $objArray = DBUtil::selectObjectById('links_links', $args['lid'], 'lid', '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}

function Web_Links_adminapi_geteditorial($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$column = &$pntable['links_editorials_column'];
    $sql = "SELECT $column[adminid],
    			   $column[editorialtimestamp],
    			   $column[editorialtext],
    			   $column[editorialtitle]
    	    FROM $pntable[links_editorials]
    	    WHERE $column[linkid]='".DataUtil::formatForStore($lid)."'";
    $result =& $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _GETFAILED);
        return false;
    }

    if ($result->EOF) {
        $status = 0;
        $result->Close();
        $editorial = array('status' => $status,
        		     	   'lid' => $lid);
    } else {
    	$status = 1;
    	$result->Close();
    	list($adminid, $editorialtimestamp, $editorialtext, $editorialtitle) = $result->fields;
        $editorial = array('adminid' => $adminid,
                           'editorialtimestamp' => $editorialtimestamp,
                           'editorialtext' => $editorialtext,
                           'editorialtitle' => $editorialtitle,
                           'status' => $status,
        		     	   'lid' => $lid);
    }

    // Return the array
    return $editorial;
}

function Web_Links_adminapi_gettotalcomments($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[ratinguser], $column[ratingcomments], $column[ratingtimestamp]
            FROM $pntable[links_votedata]
            WHERE $column[ratinglid]='".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratingcomments] != ''
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);
    $totalcomments = $result->PO_RecordCount();

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $totalcomments;
}

function Web_Links_adminapi_getcomments($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[ratinguser], $column[ratingcomments], $column[ratingtimestamp]
            FROM $pntable[links_votedata]
            WHERE $column[ratinglid]='".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratingcomments] != ''
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($ratingdbid, $ratinguser, $ratingcomments, $ratingtimestamp)=$result->fields;

        $comments[] = array('ratingdbid' => $ratingdbid,
                      		'ratinguser' => $ratinguser,
                      		'ratingcomments' => $ratingcomments,
                      		'ratingtimestamp' => $ratingtimestamp);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $comments;
}

function Web_Links_adminapi_gettotalvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[ratinguser], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
            FROM $pntable[links_votedata] WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] != 'outside'
            AND $column[ratinguser] != '".DataUtil::formatForStore(pnConfigGetVar('anonymous'))."'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);
    $totalvotes = $result->PO_RecordCount();

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $totalvotes;
}

function Web_Links_adminapi_getvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[ratinguser], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
            FROM $pntable[links_votedata] WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] != 'outside'
            AND $column[ratinguser] != '".DataUtil::formatForStore(pnConfigGetVar('anonymous'))."'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($ratingdbid, $ratinguser, $rating, $ratinghostname, $ratingtimestamp)=$result->fields;

        //Individual user information
        $column = &$pntable['links_votedata_column'];
        $result2 =& $dbconn->Execute("SELECT $column[rating]
                                      FROM $pntable[links_votedata]
                                      WHERE $column[ratinguser]='".DataUtil::formatForStore($ratinguser)."'");
        $usertotalvotes = $result2->PO_RecordCount();
        $useravgrating = 0;
        //ADODBtag MoveNext while+list+row
        while(list($rating2)=$result2->fields) {
            $useravgrating = $useravgrating + $rating2;
            $result2->MoveNext();
        }
        $useravgrating = $useravgrating / $usertotalvotes;
        $useravgrating = number_format($useravgrating, 1);

        $votes[] = array('ratingdbid' => $ratingdbid,
                   		 'ratinguser' => $ratinguser,
                         'rating' => $rating,
                         'ratinghostname' => $ratinghostname,
                         'ratingtimestamp' => $ratingtimestamp,
                         'usertotalvotes' => $usertotalvotes,
                         'useravgrating' => $useravgrating);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $votes;
}

function Web_Links_adminapi_gettotalunregvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
    		FROM $pntable[links_votedata]
            WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] = '".DataUtil::formatForStore(pnConfigGetVar('anonymous'))."'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);
    $totalunregvotes = $result->PO_RecordCount();

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $totalunregvotes;
}

function Web_Links_adminapi_getunregvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
    		FROM $pntable[links_votedata]
            WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] = '".DataUtil::formatForStore(pnConfigGetVar('anonymous'))."'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($ratingdbid, $rating, $ratinghostname, $ratingtimestamp)=$result->fields;

        $unregvotes[] = array('ratingdbid' => $ratingdbid,
                              'rating' => $rating,
                              'ratinghostname' => $ratinghostname,
                              'ratingtimestamp' => $ratingtimestamp);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $unregvotes;
}

function Web_Links_adminapi_gettotaloutvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
            FROM $pntable[links_votedata]
            WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] = 'outside'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);
    $totaloutvotes = $result->PO_RecordCount();

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $totaloutvotes;
}

function Web_Links_adminapi_getoutvotes($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($lid) || !is_numeric($lid))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_votedata_column'];
    $sql = "SELECT $column[ratingdbid], $column[rating], $column[ratinghostname], $column[ratingtimestamp]
            FROM $pntable[links_votedata]
            WHERE $column[ratinglid] = '".(int)DataUtil::formatForStore($lid)."'
            AND $column[ratinguser] = 'outside'
            ORDER BY $column[ratingtimestamp] DESC";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($ratingdbid, $rating, $ratinghostname, $ratingtimestamp)=$result->fields;

        $outvotes[] = array('ratingdbid' => $ratingdbid,
                            'rating' => $rating,
                            'ratinghostname' => $ratinghostname,
                            'ratingtimestamp' => $ratingtimestamp);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
    return $outvotes;
}

function Web_Links_adminapi_brokenlinks()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[requestid], $column[lid], $column[modifysubmitter]
            FROM $pntable[links_modrequest]
            WHERE $column[brokenlink]='1'
            ORDER BY $column[requestid]";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($requestid, $lid, $modifysubmitter)=$result->fields;

    	$column = &$pntable['links_links_column'];
    	$sql = "SELECT $column[title], $column[url], $column[submitter]
                FROM $pntable[links_links]
                WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
    	$result2 =& $dbconn->Execute($sql);


        if ($modifysubmitter != pnConfigGetVar('anonymous')) {
        	$column = &$pntable['users_column'];
        	$sql = "SELECT $column[email]
                    FROM $pntable[users]
                    WHERE $column[uname]='".DataUtil::formatForStore($modifysubmitter)."'";
        	$result3 =& $dbconn->Execute($sql);

            list($email)=$result3->fields;

        }

        list($title, $url, $owner)=$result2->fields;

        $column = &$pntable['users_column'];
        $sql = "SELECT $column[email]
                FROM $pntable[users]
                WHERE $column[uname]='".DataUtil::formatForStore($owner)."'";
        $result4 =& $dbconn->Execute($sql);

        list($owneremail)=$result4->fields;

        $brokenlinks[] = array('lid' => $lid,
                               'modifysubmitter' => $modifysubmitter,
                               'title' => $title,
                               'url' => $url,
                               'submitter' => $submitter,
                               'email' => $email,
                               'owner' => $owner,
                               'owneremail' => $owneremail);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the array
	return $brokenlinks;
}

function Web_Links_adminapi_totalmodrequests()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[requestid], $column[lid], $column[cat_id], $column[title], $column[url], $column[description], $column[modifysubmitter]
            FROM $pntable[links_modrequest]
            WHERE $column[brokenlink]='0'
            ORDER BY $column[requestid]";
    $result =& $dbconn->Execute($sql);
    $totalmodrequests = $result->PO_RecordCount();

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

	$result->Close();

	return $totalmodrequests;
}

function Web_Links_adminapi_modrequests()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_modrequest_column'];
    $sql = "SELECT $column[requestid], $column[lid], $column[cat_id], $column[title], $column[url], $column[description], $column[modifysubmitter]
            FROM $pntable[links_modrequest]
            WHERE $column[brokenlink]='0'
            ORDER BY $column[requestid]";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
    	list($requestid, $lid, $cid, $title, $url, $description, $modifysubmitter)=$result->fields;

        $column = &$pntable['links_links_column'];
        $sql = "SELECT $column[cat_id], $column[title], $column[url], $column[description], $column[submitter]
                FROM $pntable[links_links]
                WHERE $column[lid]='".(int)DataUtil::formatForStore($lid)."'";
        $result2 =& $dbconn->Execute($sql);
        list($origcid, $origtitle, $origurl, $origdescription, $owner)=$result2->fields;

        $column = &$pntable['users_column'];
        $sql = "SELECT $column[email]
                FROM $pntable[users]
                WHERE $column[uname]='".DataUtil::formatForStore($modifysubmitter)."'";
        $result3 =& $dbconn->Execute($sql);
        list($modifysubmitteremail)=$result3->fields;

        $column = &$pntable['links_categories_column'];
        $sql = "SELECT $column[title] FROM $pntable[links_categories]
                WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
        $result4 =& $dbconn->Execute($sql);
        list($cidtitle) = $result4->fields;

        $column = &$pntable['links_categories_column'];
        $sql = "SELECT $column[title] FROM $pntable[links_categories]
                WHERE $column[cat_id]='".(int)DataUtil::formatForStore($origcid)."'";
        $result5 =& $dbconn->Execute($sql);
        list($origcidtitle) = $result5->fields;

        $sql = "SELECT $column[email]
                FROM $pntable[users]
                WHERE $column[uname]='".DataUtil::formatForStore($owner)."'";
        $result6 =& $dbconn->Execute($sql);
        list($owneremail)=$result6->fields;

        if ($owner=="") {
            $owner="administration";
        }
        $modrequests[] = array('requestid' => $requestid,
        					  'lid' => $lid,
        					  'cid' => $cid,
        					  'title' => $title,
        					  'url' => $url,
        					  'description' => $description,
        					  'cidtitle' => $cidtitle,
                              'modifysubmitter' => $modifysubmitter,
        					  'origcid' => $origcid,
        					  'origtitle' => $origtitle,
        					  'origurl' => $origurl,
        					  'origdescription' => $origdescription,
        					  'origcidtitle' => $origcidtitle,
        					  'owner' => $owner,
        					  'modifysubmitteremail' => $modifysubmitteremail,
        					  'owneremail' => $owneremail);
    }

	$result->Close();

	return $modrequests;
}

function Web_Links_adminapi_linksmodcat($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($cid) || !is_numeric($cid)){
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['links_categories_column'];
    $sql = "SELECT $column[title], $column[cdescription]
            FROM $pntable[links_categories]
            WHERE $column[cat_id]='".(int)DataUtil::formatForStore($cid)."'";
    $result =& $dbconn->Execute($sql);

    // Check for an error with the database code
    if ($dbconn->ErrorNo() != 0) {
        error_log("DB Error: " . $dbconn->ErrorMsg());
        return false;
    }

    list($title,$cdescription) = $result->fields;

	$result->Close();

    if (pnSecAuthAction(0, 'Web_Links::Category', "$title::$cid", ACCESS_EDIT)) {
        $category = array('title' => $title,
        				  'cdescription' => $cdescription,
        				  'cid' => $cid);
    }

    return $category;
}
?>