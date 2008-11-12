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
 * init web_links module
 */
function web_links_init()
{
    // Create tables

    // creating categories table
    if (!DBUtil::createTable('links_categories')) {
        return false;
    }

    // creating editorials table
    if (!DBUtil::createTable('links_editorials')) {
        return false;
    }

    // creating links table
    if (!DBUtil::createTable('links_links')) {
        return false;
    }

    // creating modrequest table
    if (!DBUtil::createTable('links_modrequest')) {
        return false;
    }

    // creating newlink table
    if (!DBUtil::createTable('links_newlink')) {
        return false;
    }

    // creating votedata table
    if (!DBUtil::createTable('links_votedata')) {
        return false;
    }

	// web_links settings
    pnModSetVar('Web_Links', 'perpage', 10);
    pnModSetVar('Web_Links', 'anonwaitdays', 1);
    pnModSetVar('Web_Links', 'outsidewaitdays', 1);
    pnModSetVar('Web_Links', 'useoutsidevoting', 1);
    pnModSetVar('Web_Links', 'anonweight', 10);
    pnModSetVar('Web_Links', 'outsideweight', 20);
    pnModSetVar('Web_Links', 'detailvotedecimal', 2);
    pnModSetVar('Web_Links', 'mainvotedecimal', 1);
    pnModSetVar('Web_Links', 'toplinkspercentrigger', 0);
    pnModSetVar('Web_Links', 'toplinks', 25);
    pnModSetVar('Web_Links', 'mostpoplinkspercentrigger', 0);
    pnModSetVar('Web_Links', 'mostpoplinks', 25);
    pnModSetVar('Web_Links', 'featurebox', 1);
    pnModSetVar('Web_Links', 'linkvotemin', 5);
    pnModSetVar('Web_Links', 'blockunregmodify', 0);
    pnModSetVar('Web_Links', 'popular', 500);
    pnModSetVar('Web_Links', 'newlinks', 10);
    pnModSetVar('Web_Links', 'bestlinks', 10);
    pnModSetVar('Web_Links', 'linksresults', 10);
    pnModSetVar('Web_Links', 'links_anonaddlinklock', 1);

    // Initialisation successful
    return true;

}

/**
 * upgrade
 */
function web_links_upgrade($oldversion)
{
	// Get database information
	$dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

	$prefix = pnConfigGetVar('prefix');

	switch($oldversion) {
		case '1.0':

        pnModSetVar('Web_Links', 'perpage', 10);
        pnModSetVar('Web_Links', 'anonwaitdays', 1);
        pnModSetVar('Web_Links', 'outsidewaitdays', 1);
        pnModSetVar('Web_Links', 'useoutsidevoting', 1);
        pnModSetVar('Web_Links', 'anonweight', 10);
        pnModSetVar('Web_Links', 'outsideweight', 20);
        pnModSetVar('Web_Links', 'detailvotedecimal', 2);
        pnModSetVar('Web_Links', 'mainvotedecimal', 1);
        pnModSetVar('Web_Links', 'toplinkspercentrigger', 0);
        pnModSetVar('Web_Links', 'toplinks', 25);
        pnModSetVar('Web_Links', 'mostpoplinkspercentrigger', 0);
        pnModSetVar('Web_Links', 'mostpoplinks', 25);
        pnModSetVar('Web_Links', 'featurebox', 1);
        pnModSetVar('Web_Links', 'linkvotemin', 5);
        pnModSetVar('Web_Links', 'blockunregmodify', 0);
        pnModSetVar('Web_Links', 'popular', 500);
        pnModSetVar('Web_Links', 'newlinks', 10);
        pnModSetVar('Web_Links', 'bestlinks', 10);
        pnModSetVar('Web_Links', 'linksresults', 10);
        pnModSetVar('Web_Links', 'links_anonaddlinklock', 1);

       	break;
    }
    // Upgrade successful
    return true;
}

/**
 * delete the web_links module
 */
function web_links_delete()
{
    // Delete tables

    if (!DBUtil::dropTable('links_categories')) {
        return false;
    }

    if (!DBUtil::dropTable('links_editorials')) {
        return false;
    }

    if (!DBUtil::dropTable('links_links')) {
        return false;
    }

    if (!DBUtil::dropTable('links_modrequest')) {
        return false;
    }

    if (!DBUtil::dropTable('links_newlink')) {
        return false;
    }

    if (!DBUtil::dropTable('links_votedata')) {
        return false;
    }

	// remove module vars
	pnModDelVar('Web_Links');

    // Deletion successful
    return true;
}

?>