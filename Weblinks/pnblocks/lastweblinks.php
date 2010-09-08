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
function WebLinks_lastweblinksblock_init()
{
    // Security
    pnSecAddSchema('WeblinksBlock::', 'Block title::');
}

/**
 * get information on block
 */
function WebLinks_lastweblinksblock_info()
{
    // Values
    return array('text_type' => 'lastweblinks',
                 'module' => 'Weblinks',
                 'text_type_long' => 'Latest Web Links',
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

    // Create output object
    $pnRender = pnRender::getInstance('Weblinks', false);

    //  Check if the block is cached
    if ($pnRender->is_cached('weblinks_block_lastweblinks.html')) {
        $blockinfo['content'] = $pnRender->fetch('weblinks_block_lastweblinks.html');
        return pnBlockThemeBlock($blockinfo);
    }

    $pnRender->assign('weblinks', pnModAPIFunc('Weblinks', 'user', 'lastweblinks'));
    $pnRender->assign('tb', pnModGetVar('Weblinks', 'targetblank'));

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('weblinks_block_lastweblinks.html');

    return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 */
function Weblinks_lastweblinksblock_modify($blockinfo)
{
    // Create output object
    $pnRender = pnRender::getInstance('Weblinks', false);

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['items'])) {
        $vars['items'] = 10;
    }

    // assign the block vars
    $pnRender->assign($vars);

    // Return output
//    return $pnRender->fetch('weblinks_block_displayfeed_modify.htm');
}

/**
 * update block settings
 */
function Weblinks_lastweblinksblock_update($blockinfo)
{
    $vars['items'] = FormUtil::getPassedValue('numitems', 10, 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    return $blockinfo;
}