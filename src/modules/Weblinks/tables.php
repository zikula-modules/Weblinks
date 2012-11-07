<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: pntables.php 113 2010-09-12 18:46:32Z Petzi-Juist $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Weblinks table information
*/
function Weblinks_tables()
{
    // Initialise table array
    $dbtable = array();

    // Full table definition
    $dbtable['links_categories'] = DBUtil::getLimitedTablename('links_categories');
    $dbtable['links_categories_column']     = array ('cat_id'       => 'pn_cat_id',
                                                     'parent_id'    => 'pn_parent_id',
                                                     'title'        => 'pn_title',
                                                     'cdescription' => 'pn_description');
    $dbtable['links_categories_column_def'] = array ('cat_id'       => 'I AUTO PRIMARY',
                                                     'parent_id'    => 'I DEFAULT NULL',
                                                     'title'        => "C(50) NOTNULL DEFAULT ''",
                                                     'cdescription' => "X NOTNULL DEFAULT ''");

    $dbtable['links_links'] = DBUtil::getLimitedTablename('links_links');
    $dbtable['links_links_column']     = array ('lid'               => 'pn_lid',
                                                'cat_id'            => 'pn_cat_id',
                                                'title'             => 'pn_title',
                                                'url'               => 'pn_url',
                                                'description'       => 'pn_description',
                                                'date'              => 'pn_date',
                                                'name'              => 'pn_name',
                                                'email'             => 'pn_email',
                                                'hits'              => 'pn_hits',
                                                'submitter'         => 'pn_submitter',
                                                'linkratingsummary' => 'pn_ratingsummary',
                                                'totalvotes'        => 'pn_totalvotes',
                                                'totalcomments'     => 'pn_totalcomments');
    $dbtable['links_links_column_def'] = array ('lid'               => 'I AUTO PRIMARY',
                                                'cat_id'            => "I NOTNULL DEFAULT '0'",
                                                'title'             => "C(100) NOTNULL DEFAULT ''",
                                                'url'               => "C(254) NOTNULL DEFAULT ''",
                                                'description'       => "X NOTNULL DEFAULT ''",
                                                'date'              => 'T DEFAULT NULL',
                                                'name'              => "C(100) NOTNULL DEFAULT ''",
                                                'email'             => "C(100) NOTNULL DEFAULT ''",
                                                'hits'              => "I NOTNULL DEFAULT '0'",
                                                'submitter'         => "C(60) NOTNULL DEFAULT ''",
                                                'linkratingsummary' => "F NOTNULL DEFAULT '0.0000'",
                                                'totalvotes'        => "I NOTNULL DEFAULT '0'",
                                                'totalcomments'     => "I NOTNULL DEFAULT '0'");

    $dbtable['links_modrequest'] = DBUtil::getLimitedTablename('links_modrequest');
    $dbtable['links_modrequest_column']     = array ('requestid'       => 'pn_requestid',
                                                     'lid'             => 'pn_lid',
                                                     'cat_id'          => 'pn_cat_id',
                                                     'sid'             => 'pn_sid',
                                                     'title'           => 'pn_title',
                                                     'url'             => 'pn_url',
                                                     'description'     => 'pn_description',
                                                     'modifysubmitter' => 'pn_modifysubmitter',
                                                     'brokenlink'      => 'pn_brokenlink');
    $dbtable['links_modrequest_column_def'] = array ('requestid'       => 'I AUTO PRIMARY',
                                                     'lid'             => "I NOTNULL DEFAULT '0'",
                                                     'cat_id'          => "I NOTNULL DEFAULT '0'",
                                                     'sid'             => "I NOTNULL DEFAULT '0'",
                                                     'title'           => "C(100) NOTNULL DEFAULT ''",
                                                     'url'             => "C(254) NOTNULL DEFAULT ''",
                                                     'description'     => "X NOTNULL DEFAULT ''",
                                                     'modifysubmitter' => "C(60) NOTNULL DEFAULT ''",
                                                     'brokenlink'      => "I1 NOTNULL DEFAULT '0'");

    $dbtable['links_newlink'] = DBUtil::getLimitedTablename('links_newlink');
    $dbtable['links_newlink_column']     = array ('lid'         => 'pn_lid',
                                                  'cat_id'      => 'pn_cat_id',
                                                  'title'       => 'pn_title',
                                                  'url'         => 'pn_url',
                                                  'description' => 'pn_description',
                                                  'name'        => 'pn_name',
                                                  'email'       => 'pn_email',
                                                  'submitter'   => 'pn_submitter');
    $dbtable['links_newlink_column_def'] = array ('lid'         => 'I AUTO PRIMARY',
                                                  'cat_id'      => "I NOTNULL DEFAULT '0'",
                                                  'title'       => "C(100) NOTNULL DEFAULT ''",
                                                  'url'         => "C(254) NOTNULL DEFAULT ''",
                                                  'description' => "X NOTNULL DEFAULT ''",
                                                  'name'        => "C(100) NOTNULL DEFAULT ''",
                                                  'email'       => "C(100) NOTNULL DEFAULT ''",
                                                  'submitter'   => "C(60) NOTNULL DEFAULT ''");

    $dbtable['links_votedata'] = DBUtil::getLimitedTablename('links_votedata');
    $dbtable['links_votedata_column']     = array ('ratingdbid'      => 'pn_id',
                                                   'ratinglid'       => 'pn_lid',
                                                   'ratinguser'      => 'pn_user',
                                                   'rating'          => 'pn_rating',
                                                   'ratinghostname'  => 'pn_hostname',
                                                   'ratingcomments'  => 'pn_comments',
                                                   'ratingtimestamp' => 'pn_timestamp');
    $dbtable['links_votedata_column_def'] = array ('ratingdbid'      => 'I AUTO PRIMARY',
                                                   'ratinglid'       => "I NOTNULL DEFAULT '0'",
                                                   'ratinguser'      => "C(60) NOTNULL DEFAULT ''",
                                                   'rating'          => "I NOTNULL DEFAULT '0'",
                                                   'ratinghostname'  => "C(60) NOTNULL DEFAULT ''",
                                                   'ratingcomments'  => "X NOTNULL DEFAULT ''",
                                                   'ratingtimestamp' => "T NOTNULL DEFAULT '1970-01-01 00:00:00'");

    return $dbtable;

}