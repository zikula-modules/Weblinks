<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: randomweblinks.php 167 2010-10-19 18:08:01Z Petzi-Juist $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * initialise block
 */
function Weblinks_randomweblinksblock_init()
{
    // Security
    SecurityUtil::registerPermissionSchema('WeblinksBlock::', 'Block title::');
}

/**
 * get information on block
 */
function Weblinks_randomweblinksblock_info()
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Values
    return array('text_type' => 'randomweblinks',
                 'module' => __('Weblinks', $dom),
                 'text_type_long' => __('Random Weblinks', $dom),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true,
                 'admin_tableless' => true);
}

/**
 * display block
 */
function Weblinks_randomweblinksblock_display($blockinfo)
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
    if ($render->is_cached('weblinks_block_randomweblinks.html')) {
        $blockinfo['content'] = $render->fetch('weblinks_block_randomweblinks.html');
        return BlockUtil::themeBlock($blockinfo);
    }

    $render->assign('links', ModUtil::apiFunc('Weblinks', 'user', 'random', array('num' => $vars['limit'])));
    $render->assign('tb', ModUtil::getVar('Weblinks', 'targetblank'));

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('weblinks_block_randomweblinks.html');

    return BlockUtil::themeBlock($blockinfo);
}

/**
 * modify block settings
 */
function Weblinks_randomweblinksblock_modify($blockinfo)
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
function Weblinks_randomweblinksblock_update($blockinfo)
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
    $render->clear_cache('weblinks_block_randomweblinks.html');

    return $blockinfo;
}