<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: pnversion.php 164 2010-10-18 17:41:48Z Petzi-Juist $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

$dom = ZLanguage::getModuleDomain('Weblinks');

/**
* set modversion info
*/
$modversion['name']             = 'Weblinks';
$modversion['oldnames']         = array('Web_Links');
$modversion['displayname']      = __('Weblinks', $dom);
$modversion['description']      = __('Weblinks Module', $dom);
//! this defines the module's url
$modversion['url']              = __('weblinks', $dom);
$modversion['version']          = '2.1.0';
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