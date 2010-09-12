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
* set modversion info
*/

$modversion['name']             = 'Weblinks';
$modversion['oldnames']         = array('Web_Links');
$modversion['displayname']      = 'Weblinks';
$modversion['description']      = 'Weblinks Module';
$modversion['version']          = '2.0.1';
$modversion['credits']          = 'pndocs/credits.txt';
$modversion['help']             = 'pndocs/install.txt';
$modversion['changelog']        = 'pndocs/changelog.txt';
$modversion['license']          = 'pndocs/license.txt';
$modversion['official']         = 0;
$modversion['author']           = 'Petzi-Juist';
$modversion['contact']          = 'http://www.petzi-juist.de/';
$modversion['admin']            = 1;
$modversion['user']             = 1;
$modversion['securityschema']   = array('Weblinks::Category' => 'Category name::Category ID',
                                        'Weblinks::Link' => '::');