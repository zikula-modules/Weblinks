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
 * initialise block
 */
function Weblinks_lastweblinksblock_init()
{
    // Security
    pnSecAddSchema('WeblinksBlock::', 'Block title::');
}

/**
 * get information on block
 */
function Weblinks_lastweblinksblock_info()
{
    $dom = ZLanguage::getModuleDomain('Weblinks');

    // Values
    return array('text_type' => 'lastweblinks',
                 'module' => __('Weblinks', $dom),
                 'text_type_long' => __('Latest Weblinks', $dom),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true,
                 'admin_tableless' => true);
}

/**
 * display block
 */
function Weblinks_lastweblinksblock_display($blockinfo)
{
    // Security check
    if (!SecurityUtil::checkPermission('WeblinksBlock::', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    // check if the module is available
    if (!pnModAvailable('Weblinks')) {
        return;
    }

    // Break out options from our content field
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (!isset($vars['limit'])) {
        $vars['limit'] = 5;
    }

    // Create output object
    $render = pnRender::getInstance('Weblinks', false);

    //  Check if the block is cached
    if ($render->is_cached('weblinks_block_lastweblinks.html')) {
        $blockinfo['content'] = $render->fetch('weblinks_block_lastweblinks.html');
        return pnBlockThemeBlock($blockinfo);
    }

    $render->assign('weblinks', pnModAPIFunc('Weblinks', 'user', 'lastweblinks', array('lastlinks' => $vars['limit'])));
    $render->assign('tb', pnModGetVar('Weblinks', 'targetblank'));

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('weblinks_block_lastweblinks.html');

    return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 */
function Weblinks_lastweblinksblock_modify($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['limit'])) {
        $vars['limit'] = 5;
    }

    // Create output object
    $render = pnRender::getInstance('Weblinks', false);

    // assign the block vars
    $render->assign($vars);

    // Return output
    return $render->fetch('weblinks_block_weblinks_modify.html');
}

/**
 * update block settings
 */
function Weblinks_lastweblinksblock_update($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['limit'] = (int)FormUtil::getPassedValue('limit', 5, 'POST');

    // Security check
    if (!SecurityUtil::checkPermission('WeblinksBlock::', "$blockinfo[title]::", ACCESS_ADMIN)) {
        return false;
    }

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

    // clear the block cache
    $render = pnRender::getInstance('Weblinks', false);
    $render->clear_cache('weblinks_block_lastweblinks.html');

    return $blockinfo;
}