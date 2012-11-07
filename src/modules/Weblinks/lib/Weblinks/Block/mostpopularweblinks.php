<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: mostpopularweblinks.php 167 2010-10-19 18:08:01Z Petzi-Juist $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * initialise block
 */
function Weblinks_mostpopularweblinksblock_init()
{
    // Security
    SecurityUtil::registerPermissionSchema('WeblinksBlock::', 'Block title::');
}

/**
 * get information on block
 */
function Weblinks_mostpopularweblinksblock_info()
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Values
    return array('text_type' => 'mostpopularweblinks',
                 'module' => __('Weblinks', $dom),
                 'text_type_long' => __('Most Popular Weblinks', $dom),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true,
                 'admin_tableless' => true);
}

/**
 * display block
 */
function Weblinks_mostpopularweblinksblock_display($blockinfo)
{
    // Security check
    if (!SecurityUtil::checkPermission('WeblinksBlock::', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    // check if the module is available
    if (!ModUtil::available('Weblinks')) {
        return;
    }

    // Break out options from our content field
    $vars = BlockUtil::varsFromContent($blockinfo['content']);

    // Defaults
    if (!isset($vars['limit'])) {
        $vars['limit'] = 5;
    }

    // Create output object
    $render = Zikula_View::getInstance('Weblinks', false);

    //  Check if the block is cached
    if ($render->is_cached('weblinks_block_mostpopularweblinks.html')) {
        $blockinfo['content'] = $render->fetch('weblinks_block_mostpopularweblinks.html');
        return BlockUtil::themeBlock($blockinfo);
    }

    $render->assign('weblinks', ModUtil::apiFunc('Weblinks', 'user', 'mostpopularweblinks', array('lastlinks' => $vars['limit'])));
    $render->assign('tb', ModUtil::getVar('Weblinks', 'targetblank'));

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('weblinks_block_mostpopularweblinks.html');

    return BlockUtil::themeBlock($blockinfo);
}

/**
 * modify block settings
 */
function Weblinks_mostpopularweblinksblock_modify($blockinfo)
{
    // Get current content
    $vars = BlockUtil::varsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['limit'])) {
        $vars['limit'] = 5;
    }

    // Create output object
    $render = Zikula_View::getInstance('Weblinks', false);

    // assign the block vars
    $render->assign($vars);

    // Return output
    return $render->fetch('weblinks_block_weblinks_modify.html');
}

/**
 * update block settings
 */
function Weblinks_mostpopularweblinksblock_update($blockinfo)
{
    // Get current content
    $vars = BlockUtil::varsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['limit'] = (int)FormUtil::getPassedValue('limit', 5, 'POST');

    // Security check
    if (!SecurityUtil::checkPermission('WeblinksBlock::', "$blockinfo[title]::", ACCESS_ADMIN)) {
        return false;
    }

    // write back the new contents
    $blockinfo['content'] = BlockUtil::varsToContent($vars);

    // clear the block cache
    $render = Zikula_View::getInstance('Weblinks', false);
    $render->clear_cache('weblinks_block_mostpopularweblinks.html');

    return $blockinfo;
}